Shitty Script
===================
Unused production scripts. Must be used only for one-off actions.

----------

videos.php 
-------------

###Constant 
			- JSON_FILE : Absolute path of the json file
			- THUMBS_DIR : Absolute path of the folder thumbs
			- THUMB_WIDTH : Width of the thumb
			- THUMB_HEIGHT : Height of the thumb
			
###Process
        	- Scan a json file ( see example ) that contains a list of url video ( youtube, dailymotion , viemo )
        	- Find the thumbnail of each video
        	- record raw file
        	- The crop through the center
        	- record crop image
        
	
```
videos.json
[
   {
     "video_id":1,
     "video_url":"xxxxx"
   },
   {
     "video_id":2,
     "video_url":"xxxxx"
   },
   ...
]
```
