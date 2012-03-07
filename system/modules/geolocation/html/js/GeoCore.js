/**
 * Class Geolocation Core
 *
 * Provide methods for Geolocation.
 * 
 * @copyright  MEN AT WORK 2011-2012
 * @package    geolocation
 */
var Geolocation = new Class({
    // Implements --------------------------------------------------------------
    Implements: Options,    
    // Option set --------------------------------------------------------------
    options: {
        // Config
        debug: false,
        cookie: false,
        cookieLifeTime: 1
    }, 
    // Vars --------------------------------------------------------------------
    vars: {
        cookie: "",
        denyGeoLocation: false,
        loadByCookie: false,
        lat: 0,
        lon: 0,
        cacheID: ""        
    },
    // Initialize function -----------------------------------------------------
    initialize: function(options){
        this.setOptions(options);
    },    
    // Setter / Getter ---------------------------------------------------------
    setDebug: function(debug)
    {
        this.options.debug = debug;
    },    
    setCookie: function(cookie)
    {
        this.options.cookie = cookie;
    },
    setCookieLifetime: function(lifetime)
    {
        this.options.cookieLifeTime = lifetime;
    },    
    // Run ---------------------------------------------------------------------
    run: function ()
    {     
        this.runGeolocation();
    },    
    // Cookies -----------------------------------------------------------------    
    updateCookie: function()
    {
        // Check if the cookie function is on
        if(this.options.cookie == false || this.vars.loadByCookie == true)
        {
            return;
        }
        
        var cookieValues = {
            lat: this.vars.lat, 
            lon: this.vars.lon,
            cacheID: this.cacheID
        };
        
        var cookieOption = {
            duration: this.options.cookieLifeTime
        }
        
        if(this.options.debug == true)
        {
            console.log("Set cookie:");
            console.log(cookieValues);
            console.log(cookieOption);
        }
        
        Cookie.write('Geolocation', JSON.encode(cookieValues),cookieOption);
    },
    removeCookie: function()
    {
        Cookie.dispose('Geolocation');
    },
    // Get information ---------------------------------------------------------
    runGeolocation: function()
    {
        this.onProgress();
        
        if(this.vars.loadByCookie)
        {
            if(this.options.debug == true) console.log("Get location by cookie");
            
            this.updatePosition();
        }       
        else if (navigator.geolocation) {
            
            navigator.geolocation.getCurrentPosition(
                function(position)
                {
                    this.vars.lat = position.coords.latitude;
                    this.vars.lon = position.coords.longitude;
                                        
                    this.updatePosition();
                }.bind(this),                
                function(error) {
                    if(this.options.debug == true)
                    {                        
                        this.updateError(error.code);
                    }                    
                }.bind(this)
                );
        }
        else
        {
            if(this.options.debug == true) console.log("Geolocation is not supported by this browser");
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
                "country"       : this.vars.country,
                "countryShort"  : this.vars.countryShort,
                "REQUEST_TOKEN" : REQUEST_TOKEN
            }
        }
        else
        {
            data = {
                "action"        : "GeoSetLocation",
                "lat"           : this.vars.lat,
                "lon"           : this.vars.lon,
                "country"       : this.vars.country,
                "countryShort"  : this.vars.countryShort
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
                
                // Check if server send success
                if(json.content.success == true)
                {
                    // Update Cookie
                    this.cacheID = json.content.cache_id;
                    this.updateCookie();    
                    
                    // To the onSuccess method
                    this.afterProgress();
                    this.onSuccess();
                }
                else
                {
                    // Remove Cookie
                    this.removeCookie();
                    
                    // Show Debug information
                    if(this.options.debug == true)
                    {
                        console.log("Error by server");
                        console.log(responseElements);
                    }
                    
                    this.afterProgress();
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
                
                // Call the onFailer method
                this.afterProgress();
                this.onFailure(20);
            }.bind(this),
            onFailure:function(json,responseElements){
                // Remove cookie
                this.removeCookie(); 
                
                //Show debug
                if(this.options.debug == true)
                {
                    console.log("Error by Json Request");
                    console.log(responseElements);
                }
                
                // Call the onFailer method
                this.afterProgress();
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
                "REQUEST_TOKEN" : REQUEST_TOKEN
            }
        }
        else
        {
            data = {
                "action"        : "GeoSetError",
                "errID"         : errorID
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
                
                // Call the onFailer method
                this.afterProgress();
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
                
                // Call the onFailer method
                this.afterProgress();
                this.onFailure(20);
            }.bind(this),
            onFailure:function(json,responseElements){                               
                // Debug Information
                if(this.options.debug == true)
                {
                    console.log("Error by Json Request");
                    console.log(responseElements);
                }
                
                // Call the onFailer method
                this.afterProgress();
                this.onFailure(20);
            }.bind(this)
        }).send();
    },    
    // Helper Functions --------------------------------------------------------
    onProgress: function()
    {        
        if($("geoLocationInformation"))
        {
            $("geoLocationInformation").set("html", geo_msc_Start);
        }
        
        
    },
    afterProgress: function()
    {
        if($("geoLocationInformation"))
        {
            $("geoLocationInformation").set("html", geo_msc_Finished);
        }
    },
    onSuccess: function()
    {
    //window.location.reload();
    },    
    onFailure: function(errorID)
    {
        if($("geoLocationInformation"))
        {
            switch (errorID) {            
                case 1: // Premission Denied
                    $("geoLocationInformation").set("text", geo_err_PremissionDenied);
                    break;
                case 2: // Position unavailable
                    $("geoLocationInformation").set("text", geo_err_PositionUnavailable);
                    break;
                case 3: // Time out
                    $("geoLocationInformation").set("text", geo_err_TimeOut);
                    break;
                case 10: // Unsupported Browser
                    $("geoLocationInformation").set("text", geo_err_UnsupportedBrowser);
                    break;
                case 20: // Connection problems for AJAX
                    $("geoLocationInformation").set("text", geo_err_NoConnection);
                    break;
                default: // Unknown Error
                    $("geoLocationInformation").set("text", geo_err_UnknownError);
                    break;
            }
        }
    }
});

var RunGeolocation = new Geolocation();

RunGeolocation.setDebug(true);
RunGeolocation.setCookie(geo_cookieEnabeld);
RunGeolocation.setCookieLifetime(geo_cookieDurationW3C);

window.addEvent('domready', function(){
    RunGeolocation.run(); 	
});



