create database nepalnewsbank;
use nepalnewsbank ;

create table nas(
	
    id int primary key auto_increment ,
	
    newsid varchar(20) unique ,
	created_date  datetime,    
    local_published_date date,
    
    byline  varchar(300),
    category_list text,

    videolong  text,
	videolazy  text,
    thumbnail  text,   
    audio  text,
	photos  text,
    newsbody  text,  
    videoextra  text,
    
    tag_list  text,   
    

    uploaded_by varchar(300),
    reporter varchar(300),
    camera_man varchar(300),
    district varchar(100),
    video_type varchar(30),
     series text
    

    
   

);
drop table nas;
select * from nas;
alter table nas drop column  news_language;
alter table nas add column series text ;


create table web(
	
    id int primary key auto_increment ,
	
    newsid varchar(20) unique ,
    
    videolong  text,
	videolazy  text,
    previewgif  text,
    thumbnail  text,  
    audio  text,
    photos  text,   
    videoextra  varchar(200),
    
    newsbody  text,
   
	pushed_by varchar(200),
    pushed_date  datetime,
    wp_post_id bigint,
	vimeo_videolong text,
    vimeo_videolazy text,
    vimeo_video_extra text,
    wp_media_id text,
    
     FOREIGN KEY(newsid) REFERENCES nas (newsid)

);

drop table web ;
select * from web ;
update web set wp_post_id=null where id=1;

alter table web add column wp_media_id text;
SELECT nas.byline, nas.newsid 
FROM nas
INNER JOIN web ON nas.newsid=web.newsid where web.wp_post_id order by web.pushed_date desc limit 0;


create table archive_video(
	
    id int primary key auto_increment ,
	archive_id varchar(30) unique,
	created_date date ,
    title text,
    series int ,
    tags text,
    video text,
    thumbnail text,
    
    published_date datetime,
    wp_id text,
    wp_media_id text

);
drop table archive ;
select * from archive ;
ALTER TABLE archive
RENAME TO archive_video;
create table archive_photos(
	
    id int primary key auto_increment ,
	archive_id varchar(30) unique,
	created_date date ,
    title text,
    series int ,
    tags text,
    gallery text,
    thumbnail text,
    
    published_date datetime,
    wp_id text,
    wp_media_id text

);
select * from archive_photos;
alter table archive_photos add column  wp_id text;
alter table archive_photos add column  wp_media_id text;
drop table archive_photos ;

create table interview(
	
    id int primary key auto_increment ,
	interview_id varchar(30) unique,
	created_date date ,
    title text,
    series int ,
    tags text,
    video text,
    thumbnail text,
    body text,
    
    published_date datetime

);
drop table interview;

/*
	create table login_nas
	(
		id int primary key auto_increment ,
		username varchar(100) unique,
		password text
		
	);
	select * from login_nas ;
	drop table login_nas;

	insert into login_nas (username, password) values ('nepalnewsbank' , '$2y$10$hmMWDfxdyK0/V60UohYYF..QfYJNyI5xaGO02vOiYmXmaK0863dsK') ; 

	delete from web where newsid = '28299'
*/

