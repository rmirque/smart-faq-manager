<?php
/**
 * FAQ matching algorithm
 *
 * @package Smart_FAQ_Manager
 */

if (!defined('ABSPATH')) {
    exit;
}

/**
 * Class Smart_FAQ_Matcher
 */
class Smart_FAQ_Matcher {
    
    /**
     * Find matching FAQs for a page
     *
     * @param int $page_id Page ID
     * @param array $args Arguments
     * @return array Array of FAQ objects with scores
     */
    public static function find_matching_faqs($page_id, $args = array()) {
        $defaults = array(
            'limit' => get_option('smart_faq_max_display', 5),
            'threshold' => get_option('smart_faq_matching_threshold', 0.2), // Balanced threshold
            'category' => '',
            'use_cache' => get_option('smart_faq_enable_cache', 1),
            'min_results' => 0, // Minimum results to return even if below threshold
        );
        
        $args = wp_parse_args($args, $defaults);
        
        // Check for manual selection first
        $manual_mode = get_post_meta($page_id, '_smart_faq_manual_mode', true);
        $manual_faqs = get_post_meta($page_id, '_smart_faq_selected', true);
        
        // Manual Only mode - return only manually selected FAQs
        if ($manual_mode === 'manual' && !empty($manual_faqs)) {
            return self::get_manual_faqs($manual_faqs, $args['limit']);
        }
        
        // Extract and analyze page content
        $page_content = Smart_FAQ_Content_Analyzer::extract_page_content($page_id);
        $content_hash = Smart_FAQ_Content_Analyzer::calculate_content_signature($page_content);
        
        // Check cache first (but not for manual modes)
        if ($args['use_cache'] && $manual_mode !== 'supplement') {
            $cached_faqs = Smart_FAQ_Cache_Manager::get_cached_faqs($page_id, $content_hash);
            if ($cached_faqs !== false) {
                return self::get_faqs_from_cache($cached_faqs, $args['limit']);
            }
        }
        
        // Get page keywords and phrases
        $page_keywords = Smart_FAQ_Content_Analyzer::extract_keywords($page_content, 50);
        $page_phrases = Smart_FAQ_Content_Analyzer::extract_bigrams_trigrams($page_content);
        
        // Get candidate FAQs using fulltext search (increased pool)
        $search_terms = implode(' ', array_keys(array_slice($page_keywords, 0, 15))); // Increased from 10
        $candidate_faqs = Smart_FAQ_Database::search_faqs($search_terms, 50); // Increased from 30
        
        // If no candidates from search, get all active FAQs
        if (empty($candidate_faqs)) {
            $faq_args = array('limit' => 50); // Increased from 30
            if (!empty($args['category'])) {
                $faq_args['category'] = $args['category'];
            }
            $candidate_faqs = Smart_FAQ_Database::get_active_faqs($faq_args);
        }
        
        // Calculate relevance scores for ALL candidates
        $scored_faqs = array();
        foreach ($candidate_faqs as $faq) {
            $score = self::calculate_relevance_score($faq, $page_content, $page_keywords, $page_phrases, $args);
            $faq->relevance_score = $score;
            $scored_faqs[] = $faq; // Keep ALL scored FAQs, don't filter yet
        }
        
        // Sort by score (descending)
        usort($scored_faqs, function($a, $b) {
            return $b->relevance_score <=> $a->relevance_score;
        });
        
        // Smart filtering: Get FAQs above threshold, OR if not enough, get top scoring ones
        $above_threshold = array_filter($scored_faqs, function($faq) use ($args) {
            return $faq->relevance_score >= $args['threshold'];
        });
        
        // If we have enough above threshold, use those
        if (count($above_threshold) >= $args['limit']) {
            $matched_faqs = array_slice($above_threshold, 0, $args['limit']);
        } 
        // Otherwise, fall back to top-scoring FAQs regardless of threshold
        else {
            $matched_faqs = array_slice($scored_faqs, 0, max($args['limit'], $args['min_results']));
            
            // Log if we're using fallback (for admin debugging)
            if (!empty($matched_faqs) && $matched_faqs[0]->relevance_score < $args['threshold']) {
                smart_faq_log(
                    sprintf('Page %d: Using fallback matching (top score: %.3f, threshold: %.3f)', 
                        $page_id, 
                        $matched_faqs[0]->relevance_score, 
                        $args['threshold']
                    ), 
                    'INFO'
                );
            }
        }
        
        // Handle supplement mode: Add manual FAQs first, then automatic
        if ($manual_mode === 'supplement' && !empty($manual_faqs)) {
            $manual_faq_objects = self::get_manual_faqs($manual_faqs, 999); // Get all manual FAQs
            
            // Remove any automatic matches that are already in manual selection
            $manual_ids = array_map(function($faq) { return $faq->id; }, $manual_faq_objects);
            $matched_faqs = array_filter($matched_faqs, function($faq) use ($manual_ids) {
                return !in_array($faq->id, $manual_ids);
            });
            
            // Combine: Manual first, then automatic to fill the limit
            $remaining_slots = $args['limit'] - count($manual_faq_objects);
            if ($remaining_slots > 0) {
                $automatic_faqs = array_slice($matched_faqs, 0, $remaining_slots);
                $matched_faqs = array_merge($manual_faq_objects, $automatic_faqs);
            } else {
                $matched_faqs = array_slice($manual_faq_objects, 0, $args['limit']);
            }
        }
        
        // Cache results
        if ($args['use_cache'] && !empty($matched_faqs) && $manual_mode !== 'supplement') {
            $cache_data = array();
            foreach ($matched_faqs as $faq) {
                $cache_data[] = array(
                    'id' => $faq->id,
                    'score' => isset($faq->relevance_score) ? $faq->relevance_score : 1.0,
                );
            }
            Smart_FAQ_Cache_Manager::set_cached_faqs($page_id, $content_hash, $cache_data);
        }
        
        // Apply filter
        return apply_filters('smart_faq_matched_faqs', $matched_faqs, $page_id, $args);
    }
    
    /**
     * Get manually selected FAQs
     *
     * @param array $faq_ids Array of FAQ IDs
     * @param int $limit Limit
     * @return array Array of FAQ objects
     */
    private static function get_manual_faqs($faq_ids, $limit) {
        if (empty($faq_ids) || !is_array($faq_ids)) {
            return array();
        }
        
        $faqs = array();
        
        foreach ($faq_ids as $faq_id) {
            if (count($faqs) >= $limit) {
                break;
            }
            
            $faq = Smart_FAQ_Database::get_faq($faq_id);
            if ($faq && $faq->status === 'active') {
                $faq->relevance_score = 1.0; // Manual selections get perfect score
                $faqs[] = $faq;
            }
        }
        
        return $faqs;
    }
    
    /**
     * Calculate relevance score for an FAQ
     *
     * @param object $faq FAQ object
     * @param string $page_content Page content
     * @param array $page_keywords Page keywords
     * @param array $page_phrases Page phrases
     * @param array $args Arguments
     * @return float Relevance score (0-1)
     */
    private static function calculate_relevance_score($faq, $page_content, $page_keywords, $page_phrases, $args) {
        // Get FAQ content
        $faq_content = $faq->question . ' ' . $faq->answer . ' ' . $faq->keywords;
        $faq_keywords = Smart_FAQ_Content_Analyzer::extract_keywords($faq_content, 50);
        $faq_phrases = Smart_FAQ_Content_Analyzer::extract_bigrams_trigrams($faq_content);
        
        // Get scoring weights
        $keyword_weight = get_option('smart_faq_keyword_weight', 0.4);
        $content_weight = get_option('smart_faq_content_weight', 0.3);
        $phrase_weight = get_option('smart_faq_phrase_weight', 0.2);
        $priority_weight = get_option('smart_faq_priority_weight', 0.1);
        
        // 1. Keyword matching score
        $keyword_score = Smart_FAQ_Content_Analyzer::calculate_keyword_similarity($page_keywords, $faq_keywords);
        
        // 2. Content overlap score
        $content_score = self::calculate_content_overlap($page_content, $faq_content);
        
        // 3. Phrase matching score
        $phrase_score = self::calculate_phrase_match($page_phrases, $faq_phrases);
        
        // 4. Priority score (normalize 0-100 to 0-1)
        $priority_score = min(100, max(0, $faq->priority)) / 100;
        
        // Calculate final score
        $final_score = ($keyword_score * $keyword_weight) +
                      ($content_score * $content_weight) +
                      ($phrase_score * $phrase_weight) +
                      ($priority_score * $priority_weight);
        
        // Category boost
        if (!empty($args['category']) && $faq->category === $args['category']) {
            $category_boost = get_option('smart_faq_category_boost', 1.2);
            $final_score *= $category_boost;
        }
        
        // Ensure score is between 0 and 1
        $final_score = min(1, max(0, $final_score));
        
        // Apply filter
        return apply_filters('smart_faq_relevance_score', $final_score, $faq, $page_content);
    }
    
    /**
     * Calculate content overlap between two texts
     *
     * @param string $content1 First content
     * @param string $content2 Second content
     * @return float Overlap score (0-1)
     */
    private static function calculate_content_overlap($content1, $content2) {
        $words1 = Smart_FAQ_Content_Analyzer::tokenize_content($content1);
        $words2 = Smart_FAQ_Content_Analyzer::tokenize_content($content2);
        
        if (empty($words1) || empty($words2)) {
            return 0;
        }
        
        $common_words = array_intersect($words1, $words2);
        $overlap = count($common_words);
        
        // Normalize by smaller set
        $min_size = min(count($words1), count($words2));
        
        return $min_size > 0 ? $overlap / $min_size : 0;
    }
    
    /**
     * Calculate phrase matching score
     *
     * @param array $phrases1 First set of phrases
     * @param array $phrases2 Second set of phrases
     * @return float Match score (0-1)
     */
    private static function calculate_phrase_match($phrases1, $phrases2) {
        if (empty($phrases1) || empty($phrases2)) {
            return 0;
        }
        
        $common_phrases = array_intersect_key($phrases1, $phrases2);
        
        if (empty($common_phrases)) {
            return 0;
        }
        
        // Calculate weighted match
        $score = 0;
        foreach ($common_phrases as $phrase => $weight1) {
            $weight2 = $phrases2[$phrase];
            // Phrase matches are weighted higher
            $score += min($weight1, $weight2) * 1.5;
        }
        
        // Normalize
        $size1 = array_sum($phrases1);
        $size2 = array_sum($phrases2);
        $normalizer = min($size1, $size2);
        
        return $normalizer > 0 ? min(1, $score / $normalizer) : 0;
    }
    
    /**
     * Get FAQs from cache data
     *
     * @param array $cache_data Cached FAQ data
     * @param int $limit Limit
     * @return array Array of FAQ objects
     */
    private static function get_faqs_from_cache($cache_data, $limit) {
        $faqs = array();
        
        foreach ($cache_data as $item) {
            if (count($faqs) >= $limit) {
                break;
            }
            
            $faq = Smart_FAQ_Database::get_faq($item['id']);
            if ($faq) {
                $faq->relevance_score = $item['score'];
                $faqs[] = $faq;
            }
        }
        
        return $faqs;
    }
}


