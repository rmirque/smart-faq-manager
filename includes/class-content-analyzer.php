<?php
/**
 * Content analysis engine
 *
 * @package Smart_FAQ_Manager
 */

if (!defined('ABSPATH')) {
    exit;
}

/**
 * Class Smart_FAQ_Content_Analyzer
 */
class Smart_FAQ_Content_Analyzer {
    
    /**
     * Stop words list for English
     *
     * @var array
     */
    private static $stop_words = array(
        'a', 'about', 'above', 'after', 'again', 'against', 'all', 'am', 'an', 'and', 'any', 'are', 'aren\'t', 
        'as', 'at', 'be', 'because', 'been', 'before', 'being', 'below', 'between', 'both', 'but', 'by', 
        'can\'t', 'cannot', 'could', 'couldn\'t', 'did', 'didn\'t', 'do', 'does', 'doesn\'t', 'doing', 
        'don\'t', 'down', 'during', 'each', 'few', 'for', 'from', 'further', 'had', 'hadn\'t', 'has', 
        'hasn\'t', 'have', 'haven\'t', 'having', 'he', 'he\'d', 'he\'ll', 'he\'s', 'her', 'here', 'here\'s', 
        'hers', 'herself', 'him', 'himself', 'his', 'how', 'how\'s', 'i', 'i\'d', 'i\'ll', 'i\'m', 'i\'ve', 
        'if', 'in', 'into', 'is', 'isn\'t', 'it', 'it\'s', 'its', 'itself', 'let\'s', 'me', 'more', 'most', 
        'mustn\'t', 'my', 'myself', 'no', 'nor', 'not', 'of', 'off', 'on', 'once', 'only', 'or', 'other', 
        'ought', 'our', 'ours', 'ourselves', 'out', 'over', 'own', 'same', 'shan\'t', 'she', 'she\'d', 
        'she\'ll', 'she\'s', 'should', 'shouldn\'t', 'so', 'some', 'such', 'than', 'that', 'that\'s', 'the', 
        'their', 'theirs', 'them', 'themselves', 'then', 'there', 'there\'s', 'these', 'they', 'they\'d', 
        'they\'ll', 'they\'re', 'they\'ve', 'this', 'those', 'through', 'to', 'too', 'under', 'until', 'up', 
        'very', 'was', 'wasn\'t', 'we', 'we\'d', 'we\'ll', 'we\'re', 'we\'ve', 'were', 'weren\'t', 'what', 
        'what\'s', 'when', 'when\'s', 'where', 'where\'s', 'which', 'while', 'who', 'who\'s', 'whom', 'why', 
        'why\'s', 'with', 'won\'t', 'would', 'wouldn\'t', 'you', 'you\'d', 'you\'ll', 'you\'re', 'you\'ve', 
        'your', 'yours', 'yourself', 'yourselves'
    );
    
    /**
     * Extract content from a page
     *
     * @param int $post_id Post/Page ID
     * @return string Extracted content
     */
    public static function extract_page_content($post_id) {
        $post = get_post($post_id);
        
        if (!$post) {
            return '';
        }
        
        $content = '';
        
        // Title (very high weight - include 5 times for maximum importance)
        $title = get_the_title($post_id);
        $content .= str_repeat($title . ' ', 5); // Increased from 3
        
        // Extract headings from content (high weight)
        $post_content = $post->post_content;
        $headings = self::extract_headings($post_content);
        foreach ($headings as $heading) {
            $content .= str_repeat($heading . ' ', 3); // Weight headings heavily
        }
        
        // Main content
        // Remove shortcodes
        $post_content = strip_shortcodes($post_content);
        // Remove HTML tags
        $post_content = wp_strip_all_tags($post_content);
        $content .= $post_content . ' ';
        
        // Excerpt (medium-high weight)
        if (!empty($post->post_excerpt)) {
            $content .= str_repeat($post->post_excerpt . ' ', 2); // Weight excerpt more
        }
        
        // Categories (high weight)
        $categories = get_the_category($post_id);
        if ($categories) {
            foreach ($categories as $category) {
                $content .= str_repeat($category->name . ' ', 3); // Weight categories more
            }
        }
        
        // Tags (medium weight)
        $tags = get_the_tags($post_id);
        if ($tags) {
            foreach ($tags as $tag) {
                $content .= str_repeat($tag->name . ' ', 2); // Weight tags more
            }
        }
        
        // Meta description (high weight - if using SEO plugin)
        $meta_description = get_post_meta($post_id, '_yoast_wpseo_metadesc', true);
        if ($meta_description) {
            $content .= str_repeat($meta_description . ' ', 3); // Weight meta description
        }
        
        // Also check Rank Math SEO
        if (!$meta_description) {
            $meta_description = get_post_meta($post_id, 'rank_math_description', true);
            if ($meta_description) {
                $content .= str_repeat($meta_description . ' ', 3);
            }
        }
        
        // Custom fields (if any are commonly used)
        $custom_field = get_post_meta($post_id, 'custom_description', true);
        if ($custom_field) {
            $content .= $custom_field . ' ';
        }
        
        // Apply filter for custom content extraction
        $content = apply_filters('smart_faq_content_extract', $content, $post_id);
        
        return $content;
    }
    
    /**
     * Extract headings from HTML content
     *
     * @param string $html HTML content
     * @return array Array of heading texts
     */
    private static function extract_headings($html) {
        $headings = array();
        
        // Match h1-h6 tags
        if (preg_match_all('/<h[1-6][^>]*>(.*?)<\/h[1-6]>/is', $html, $matches)) {
            foreach ($matches[1] as $heading) {
                $heading = wp_strip_all_tags($heading);
                $heading = trim($heading);
                if (!empty($heading)) {
                    $headings[] = $heading;
                }
            }
        }
        
        return $headings;
    }
    
    /**
     * Tokenize content into words
     *
     * @param string $content Content to tokenize
     * @return array Array of words
     */
    public static function tokenize_content($content) {
        // Convert to lowercase
        $content = mb_strtolower($content, 'UTF-8');
        
        // Remove special characters but keep spaces and apostrophes
        $content = preg_replace('/[^a-z0-9\s\']/u', ' ', $content);
        
        // Split into words
        $words = preg_split('/\s+/', $content, -1, PREG_SPLIT_NO_EMPTY);
        
        // Remove stop words
        $stop_words = apply_filters('smart_faq_stop_words', self::$stop_words);
        $words = array_diff($words, $stop_words);
        
        // Remove very short words (1-2 characters)
        $words = array_filter($words, function($word) {
            return mb_strlen($word, 'UTF-8') > 2;
        });
        
        return array_values($words);
    }
    
    /**
     * Extract keywords from content
     *
     * @param string $content Content to analyze
     * @param int $limit Number of keywords to return
     * @return array Array of keywords with scores
     */
    public static function extract_keywords($content, $limit = 50) {
        $words = self::tokenize_content($content);
        
        // Count word frequency
        $word_freq = array_count_values($words);
        
        // Sort by frequency
        arsort($word_freq);
        
        // Limit to top N keywords
        $keywords = array_slice($word_freq, 0, $limit, true);
        
        // Normalize scores (0-1)
        $max_freq = max($keywords);
        if ($max_freq > 0) {
            foreach ($keywords as $word => $freq) {
                $keywords[$word] = $freq / $max_freq;
            }
        }
        
        return apply_filters('smart_faq_keywords', $keywords, $content);
    }
    
    /**
     * Calculate content hash for caching
     *
     * @param string $content Content to hash
     * @return string MD5 hash
     */
    public static function calculate_content_signature($content) {
        return md5($content);
    }
    
    /**
     * Extract bigrams and trigrams from content
     *
     * @param string $content Content to analyze
     * @return array Array of phrases with scores
     */
    public static function extract_bigrams_trigrams($content) {
        $words = self::tokenize_content($content);
        $phrases = array();
        
        // Extract bigrams (2-word phrases)
        for ($i = 0; $i < count($words) - 1; $i++) {
            $bigram = $words[$i] . ' ' . $words[$i + 1];
            if (!isset($phrases[$bigram])) {
                $phrases[$bigram] = 0;
            }
            $phrases[$bigram]++;
        }
        
        // Extract trigrams (3-word phrases)
        for ($i = 0; $i < count($words) - 2; $i++) {
            $trigram = $words[$i] . ' ' . $words[$i + 1] . ' ' . $words[$i + 2];
            if (!isset($phrases[$trigram])) {
                $phrases[$trigram] = 0;
            }
            $phrases[$trigram]++;
        }
        
        // Filter out phrases that only appear once
        $phrases = array_filter($phrases, function($count) {
            return $count > 1;
        });
        
        // Sort by frequency
        arsort($phrases);
        
        // Normalize scores
        if (!empty($phrases)) {
            $max_freq = max($phrases);
            if ($max_freq > 0) {
                foreach ($phrases as $phrase => $freq) {
                    $phrases[$phrase] = $freq / $max_freq;
                }
            }
        }
        
        return $phrases;
    }
    
    /**
     * Calculate similarity between two sets of keywords
     *
     * @param array $keywords1 First set of keywords
     * @param array $keywords2 Second set of keywords
     * @return float Similarity score (0-1)
     */
    public static function calculate_keyword_similarity($keywords1, $keywords2) {
        if (empty($keywords1) || empty($keywords2)) {
            return 0;
        }
        
        // Get intersection of keywords
        $common_keywords = array_intersect_key($keywords1, $keywords2);
        
        if (empty($common_keywords)) {
            return 0;
        }
        
        // Calculate weighted overlap
        $score = 0;
        foreach ($common_keywords as $keyword => $weight1) {
            $weight2 = $keywords2[$keyword];
            $score += min($weight1, $weight2);
        }
        
        // Normalize by the size of the smaller set
        $size1 = array_sum($keywords1);
        $size2 = array_sum($keywords2);
        $normalizer = min($size1, $size2);
        
        return $normalizer > 0 ? $score / $normalizer : 0;
    }
    
    /**
     * Get stop words list
     *
     * @return array Stop words
     */
    public static function get_stop_words() {
        return apply_filters('smart_faq_stop_words', self::$stop_words);
    }
}


