-- ============================================== 1. create nas table
create table nas(
id int primary key auto_increment,
newsid varchar(20) unique,
created_date datetime,
local_published_date date,
byline varchar(300),
category_list text,
videolong text,
videolazy text,
thumbnail text,
audio text,
photos text,
newsbody text,
videoextra text,
tag_list text,
uploaded_by varchar(300),
reporter varchar(300),
camera_man varchar(300),
district varchar(100),
video_type varchar(30),
series text
);

-- ============================================== 2. create web table
create table web(
id int primary key auto_increment,
newsid varchar(20) unique,
videolong text,
videolazy text,
thumbnail text,
audio text,
photos text,
videoextra varchar(200),
newsbody text,
pushed_by varchar(200),
pushed_date datetime,
wp_post_id bigint,
vimeo_videolong text,
vimeo_videolazy text,
vimeo_video_extra text,
wp_media_id text,
wp_post_type varchar(50),
FOREIGN KEY(newsid) REFERENCES nas (newsid)
);

-- ============================================== 3. create archive_video table
-- create table archive_video(
	
--     id int primary key auto_increment ,
-- 	archive_id varchar(30) unique,
-- 	created_date date ,
--     title text,
--     series int ,
--     tags text,
--     video text,
--     thumbnail text,
    
--     published_date datetime,
--     wp_id text,
--     wp_media_id text,
--     description text

-- );
-- ============================================== 4. create archive_photos table
-- create table archive_photos(
	
--     id int primary key auto_increment ,
-- 	archive_id varchar(30) unique,
-- 	created_date date ,
--     title text,
--     series int ,
--     tags text,
--     gallery text,
--     thumbnail text,
    
--     published_date datetime,
--     wp_id text,
--     wp_media_id text,
--     description text

-- );
-- ============================================== 5. create interview table
create table interview(
id int primary key auto_increment,
interview_id varchar(30) unique,
created_date date ,
title text,
series int,
tags text,
video text,
thumbnail text,
body text,
published_date datetime
);

--  ============================================== 6. create iframe table
create table iframe(
id int primary key auto_increment,
iframe_title text,
iframe_iframe text,
iframe_text text
);
