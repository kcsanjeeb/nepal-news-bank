create database nepalnewsbank;
use nepalnewsbank ;

create table nas(
	
    id int primary key auto_increment ,
	
    newsid varchar(20) unique ,
	created_date  datetime,    
    local_published_date date,
    
    byline  varchar(300),
    category_list text,

    videolong  varchar(200),
	videolazy  varchar(200),
    previewgif  varchar(200),
    thumbnail  varchar(200),   
    audio  varchar(200),
	photos  varchar(200),
    newsbody  varchar(200),  
    videoextra  varchar(200),
    
    tag_list  text,   
    

    uploaded_by varchar(300),
    reporter varchar(300),
    camera_man varchar(300),
    district varchar(100),
    news_language varchar(100)
    
   

);
drop table nas;
select * from nas;
 ALTER TABLE  nas  
CHANGE COLUMN isexclusibe isexclusive  
boolean ; 

ALTER TABLE nas
DROP COLUMN previewgif; 

ALTER TABLE nas
DROP COLUMN subscription_type; 
     /* subscription_type varchar(100), */

     /* news_type varchar(150),    Update to exclusive boolean*/  
	/* isexclusive boolean , */
    ALTER TABLE nas
ADD  category text;

ALTER TABLE nas
CHANGE COLUMN category category_list text;


 drop table web ;
       
create table web(
	
    id int primary key auto_increment ,
	
    newsid varchar(20) unique ,
    
    videolong  varchar(200),
	videolazy  varchar(200),
    previewgif  varchar(200),
    thumbnail  varchar(200),  
    audio  varchar(200),
    photos  text,   
    videoextra  varchar(200),
    
    newsbody  text,
   
	pushed_by varchar(200),
    pushed_date  datetime,
    wp_post_id bigint,
    
     FOREIGN KEY(newsid) REFERENCES nas (newsid)

);

select * from web ;
update web set  wp_post_id = null where id = 2 ;
drop table web ;
ALTER TABLE web
ADD wp_post_id bigint;

ALTER TABLE web
DROP COLUMN previewgif;


/* 
insert into nas(newsid) values (78325);
drop table nas ; 
select * from nas ;
SELECT FLOOR(RAND() * 99999) AS news_id
        FROM nas 
        WHERE 'newsid' NOT IN (SELECT newsid FROM nas)
        LIMIT 1;
drop table local_news ;
drop table web ; 
select * from web ;
drop table web

SELECT local_published_date 
FROM nas 
WHERE local_published_date > '2021-02-21'
ORDER BY local_published_date desc
LIMIT 100 ;

SELECT local_published_date 
FROM nas 
WHERE local_published_date < NOW() 
ORDER BY local_published_date desc
LIMIT 1 ;

        SELECT local_published_date 
FROM nas 
WHERE local_published_date > '2021-02-21'
ORDER BY local_published_date desc
LIMIT 100 ;

SELECT local_published_date 
FROM nas 
WHERE local_published_date < NOW() 
ORDER BY local_published_date desc
LIMIT 1 ;
        */
        
