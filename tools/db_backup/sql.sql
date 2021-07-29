
CREATE TABLE `archives` (
  `id` int(11) PRIMARY KEY AUTO_INCREMENT NOT NULL ,
  `archive_id` varchar(30) ,
  `created_date` date L,
  `title` text ,
  `series` text ,
  `tags` text ,
  `thumbnail` text ,
  `categories` text ,
  `archive_videos` text ,
  `archive_photos` text ,
  `published_date` datetime L,
  `wp_id` text ,
  `wp_media_id` text ,
  `thumbnail_local_path` text ,
  `local_dir` text ,
  `ftp_dir` text 
) 


CREATE TABLE `iframe` (
  `id`  int(11) PRIMARY KEY AUTO_INCREMENT NOT NULL,
  `iframe_title` text ,
  `iframe_iframe` text ,
  `iframe_text` text 
) 


CREATE TABLE `nas` (
  `id` int(11) PRIMARY KEY AUTO_INCREMENT NOT NULL,
  `newsid` varchar(20) ,
  `created_date` datetime L,
  `local_published_date` date L,
  `byline` varchar(300) ,
  `category_list` text ,
  `regular_feed` text ,
  `ready_version` text ,
  `thumbnail` text ,
  `audio_complete_story` text ,
  `photos` text ,
  `news_file` text ,
  `rough_cut` text ,
  `tag_list` text ,
  `uploaded_by` varchar(300) ,
  `reporter` varchar(300) ,
  `camera_man` varchar(300) ,
  `district` varchar(100) ,
  `video_type` varchar(30) ,
  `series` text ,
  `extra_files` text ,
  `extra_files_description` text ,
  `audio_description` text ,
  `audio_bites_description` text ,
  `audio_bites` text ,
  `gallery_description` text ,
  `dir_path` text 
) 


CREATE TABLE `web` (
  `id` int(11) PRIMARY KEY AUTO_INCREMENT NOT NULL,
  `newsid` varchar(20) ,
  `regular_feed` text ,
  `ready_version` text ,
  `previewgif` text ,
  `thumbnail` text ,
  `audio_complete_story` text ,
  `photos` text ,
  `rough_cut` text ,
  `news_file` text ,
  `pushed_by` varchar(200) ,
  `pushed_date` datetime L,
  `wp_post_id` bigint(20) L,
  `vimeo_regular_feed` text ,
  `vimeo_ready_version` text ,
  `vimeo_rough_cut` text ,
  `wp_media_id` text ,
  `wp_post_type` varchar(50) ,
  `extra_files` text ,
  `audio_bites` text ,
  `ftp_dir` text ,

  FOREIGN KEY(newsid) REFERENCES nas (newsid)

) 


