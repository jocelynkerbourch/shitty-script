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

priceAlertCommand.php 
-------------

###Constant 
      - TWILIO_ACCOUNT_SID : Twilio account SID
      - TWILIO_AUTH_TOKEN : wilio account Token
      - FROM : Twilio number
      - TO : Your number

###Process
          - Scan a json file ( see example ) that contains a list of url, regex, alert and label
          - Get Html File
          - Find regex in html content
          - If the price is below the alert sending a sms
  
```
videos.json
{
  "1": {
    "url": "https://www.zalando.fr/nike-performance-free-4-0-flyknit-chaussures-de-running-legeres-n1242a0rc-q11.html",
    "regex": "/<span class=\\\"price specialPrice nowrap\\\" id=\\\"articlePrice\\\">([\\s0-9,]*)/",
    "alert": 65,
    "label": "Nike FREE 4.0 FLYKNIT"
  },
  ...
}
```
