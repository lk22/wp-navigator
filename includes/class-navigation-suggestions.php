<?php 

if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

if ( ! class_exists('WP_Navigation_Suggestions') ) {
    class WP_Navigation_Suggestions {
        private $suggestions = [
            "en_US" => [
                "post_new" => [
                    "label" => "Create a new post",
                    "path" => "Posts > Add New",
                    "url" => "post-new.php",
                ],
                "page_new" => [
                    "label" => "Create a new page",
                    "path" => "Pages > Add New",
                    "url" => "post-new.php?post_type=page",
                ],
                "user_new" => [
                    "label" => "Create a new user",
                    "path" => "Users > Add New",
                    "url" => "user-new.php",
                ],
                "category_new" => [
                    "label" => "Create a new category",
                    "path" => "Posts > Categories > Add New Category",
                    "url" => "edit-tags.php?taxonomy=category",
                ],
                "tag_new" => [
                    "label" => "Create a new tag",
                    "path" => "Posts > Tags > Add New Tag",
                    "url" => "edit-tags.php?taxonomy=post_tag",
                ],
            ],
            "da_DK" => [
                "post_new" => [
                    "label" => "Tilføj ny post",
                    "path" => "Indlæg > Tilføj nyt",
                    "url" => "post-new.php",
                ],
                "page_new" => [
                    "label" => "Tilføj ny side",
                    "path" => "Sider > Tilføj ny",
                    "url" => "post-new.php?post_type=page",
                ],
                "user_new" => [
                    "label" => "Tilføj ny bruger",
                    "path" => "Brugere > Tilføj ny",
                    "url" => "user-new.php",
                ],
                "category_new" => [
                    "label" => "Tilføj ny kategori",
                    "path" => "Indlæg > Kategorier > Tilføj ny kategori",
                    "url" => "edit-tags.php?taxonomy=category",
                ],
                "tag_new" => [
                    "label" => "Tilføj nyt tag",
                    "path" => "Indlæg > Tags > Tilføj ny tag",
                    "url" => "edit-tags.php?taxonomy=post_tag",
                ],
            ]
        ];

        public function get_suggestions( $locale = "en_US" ) {
            return $this->suggestions[$locale];
        }
    }
}

?>