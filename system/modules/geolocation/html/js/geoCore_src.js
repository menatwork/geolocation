/**
 * Class Geolocation Core
 *
 * Provide methods for Geolocation.
 * 
 * @copyright  MEN AT WORK 2012
 * @package    geolocation
 */
var Geolocation = new Class({
    Implements: [Options],   
    options: 
    {
        debug: false
    },
    infoElements: [],
    vars: 
    {        
        lat: 0,
        lon: 0,
        mode: -1
    },
    initialize: function(options)
    {
        this.setOptions(options);
    },    
    // Setter / Getter
    setDebug: function(debug)
    {
        this.options.debug = debug;
    },
    addInfoElement: function(key)
    {
        if (!this.infoElements[key])
        {
            this.infoElements[key] = [];
        }
		
        this.infoElements.include(key);
    }, 
    // Get information
    runGeolocation: function()
    {
        this.onProgress();
        
        if (navigator.geolocation) {            
            navigator.geolocation.getCurrentPosition(
                // Success
                function(position)
                {
                    this.vars.lat = position.coords.latitude;
                    this.vars.lon = position.coords.longitude;
                                        
                    this.updatePosition();
                }.bind(this), 
                // Error
                function(error) {                                          
                    this.updateError(error.code);                                        
                }.bind(this)
                );
        }
        else
        {
            if(this.options.debug == true)
            {
                console.log("Geolocation is not supported by this browser");
            }
            
            this.updateError(10);
        }
    },    
    updatePosition: function()
    {
        // Debug Information
        if(this.options.debug == true)
        {
            console.log("Start location updating");
            console.log(this.vars);
        }
                
        // Check if the request token is set
        if( typeof(REQUEST_TOKEN) !== 'undefined' )
        {
            data = {
                "action"        : "GeoSetLocation",
                "lat"           : this.vars.lat,
                "lon"           : this.vars.lon,
                "isAJAX"        : true,
                "REQUEST_TOKEN" : REQUEST_TOKEN
            }
        }
        else
        {
            data = {
                "action"        : "GeoSetLocation",
                "lat"           : this.vars.lat,
                "lon"           : this.vars.lon,
                "isAJAX"        : true
            }
        }
        
        // Debug Information
        if(this.options.debug == true)
        {
            console.log("Start Request");
            console.log(this.vars);
        }  
        
        // Send new request
        new Request.JSON({
            method:'post',
            url: "ajax.php",
            data: data,
            evalScripts:false,
            evalResponse:false,
            onSuccess:function(json,responseElements){   
                
                // Debug Information
                if(this.options.debug == true)
                {
                    console.log("On Success");
                    console.log(this.vars);
                }
                
                // Update Request Token
                if( typeof(REQUEST_TOKEN) !== 'undefined' )
                {
                    REQUEST_TOKEN = json.token;
                }
                
                // Check if server send success
                if(json.content.success == true)
                {  
                    this.onSuccess();
                }
                else
                {                    
                    // Show Debug information
                    if(this.options.debug == true)
                    {
                        console.log("Error by server");
                        console.log(responseElements);
                    }
                }
                
            }.bind(this),   
            onError:function(text, error)
            {
                // Debug Information
                if(this.options.debug == true)
                {
                    console.log("Error by Json Request");
                    console.log(error);
                }
                
                this.onFailure(20);
            }.bind(this),
            onFailure:function(json,responseElements)
            {
                // Debug Information
                if(this.options.debug == true)
                {
                    console.log("Error by Json Request");
                    console.log(responseElements);
                }
                
                this.onFailure(20)
            }.bind(this)
        }).send();
    },
    updateError: function(errorID)
    {        
        // Debug Information
        if(this.options.debug == true)
        {
            console.log("Start error updating");
            console.log(errorID);
        }
        
        // Check if the request token is set
        if( typeof(REQUEST_TOKEN) !== 'undefined' )
        {
            data = {
                "action"        : "GeoSetError",
                "errID"         : errorID,
                "isAJAX"        : true,
                "REQUEST_TOKEN" : REQUEST_TOKEN
            }
        }
        else
        {
            data = {
                "action"        : "GeoSetError",
                "errID"         : errorID,
                "isAJAX"        : true
            }
        }
                 
        // Send new request
        new Request.JSON({
            method:'post',
            url: "ajax.php",
            data: data,
            evalScripts:false,
            evalResponse:false,
            onSuccess:function(json,responseElements){   
                // Update Request Token
                if( typeof(REQUEST_TOKEN) !== 'undefined' )
                {
                    REQUEST_TOKEN = json.token;
                }                
                // Debug Information
                if(this.options.debug == true)
                {
                    console.log("Success");
                    console.log(responseElements);
                }
                
                this.onFailure(errorID);
            }.bind(this),
            onError:function(text, error)
            {
                // Debug Information
                if(this.options.debug == true)
                {
                    console.log("Error by Json Request");
                    console.log(error);
                }
                
                this.onFailure(20);
            }.bind(this),
            onFailure:function(json,responseElements){                               
                // Debug Information
                if(this.options.debug == true)
                {
                    console.log("Error by Json Request");
                    console.log(responseElements);
                }
                
                this.onFailure(20);
            }.bind(this)
        }).send();
    },    
    // Helper Functions
    updateInfoElements: function(message)
    {
        this.infoElements.each(function(key)
        {
           $(key).set("html", message);
        });
    },
    onProgress: function()
    {
        this.updateInfoElements(this.options.options.messages.geo_msc_Start);
    },
    afterProgress: function()
    {
        this.updateInfoElements(this.options.options.messages.geo_msc_Finished);
    },
    onSuccess: function()
    {
        window.location.reload();
    },    
    onFailure: function(errorID)
    {
        window.location.reload();
        
        if($("geoLocationInformation"))
        {
            switch (errorID) {            
                case 1: // Permission Denied
                    this.updateInfoElements(this.options.options.messages.geo_err_PermissionDenied);                   
                    break;
                case 2: // Position unavailable
                    this.updateInfoElements(this.options.options.messages.geo_err_PositionUnavailable);                   
                    break;
                case 3: // Time out
                    this.updateInfoElements(this.options.options.messages.geo_err_TimeOut);                   
                    break;
                case 10: // Unsupported Browser
                    this.updateInfoElements(this.options.options.messages.geo_err_UnsupportedBrowser);                   
                    break;
                case 20: // Connection problems for AJAX
                    this.updateInfoElements(this.options.options.messages.geo_err_NoConnection);                   
                    break;
                default: // Unknown Error
                    this.updateInfoElements(this.options.options.messages.geo_err_UnknownError);                   
                    break;
            }
        }
    }
});