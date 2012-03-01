/**
 * Class GeoProtection Core
 *
 * Provide methods for GeoProtection.
 * 
 * @copyright  MEN AT WORK 2011-2012
 * @package    GeoProtection
 */
var GeoProtection = new Class({
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
        country: "",
        countryShort: ""
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
        this.checkCookies();        
        this.runGeolocation();
    },    
    // Cookies -----------------------------------------------------------------
    checkCookies: function()
    {
        // Check if the cookie function is on
        if(this.options.cookie != true)
        {
            return;
        }
        
        // Check if a cookie is set
        this.vars.cookie = Cookie.read('GeoProtectionUser');
               
        if(this.options.debug == true)
        {
            console.log("Get cookie:");
            console.log(this.vars.cookie);
        }
        
        if(this.vars.cookie == null)
        {
            return;
        }
        
        // Decode cookie information
        this.vars.cookie = JSON.decode(this.vars.cookie);
        
        this.vars.lon = this.vars.cookie.lon;
        this.vars.lat = this.vars.cookie.lat;      
        this.vars.country = this.vars.cookie.country;
        this.vars.countryShort = this.vars.cookie.countryShort;
        this.vars.loadByCookie = true;        
    },    
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
            country: this.vars.country,
            countryShort: this.vars.countryShort
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
        
        Cookie.write('GeoProtectionUser', JSON.encode(cookieValues),cookieOption);
    },
    removeCookie: function()
    {
        Cookie.dispose('GeoProtectionUser');
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
                "action"        : "GeoProSetError",
                "errID"         : errorID,
                "REQUEST_TOKEN" : REQUEST_TOKEN
            }
        }
        else
        {
            data = {
                "action"        : "GeoProSetError",
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
                "action"        : "GeoProSetLocation",
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
                "action"        : "GeoProSetLocation",
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
    // Helper Functions --------------------------------------------------------
    onProgress: function()
    {        
        $("geoLocationInformation").set("html", gp_msc_Start);
    },
    afterProgress: function()
    {
        $("geoLocationInformation").set("html", gp_msc_Finished);
    },
    onSuccess: function()
    {
        window.location.reload();
    },    
    onFailure: function(errorID)
    {
        switch (errorID) {            
            case 1: // Premission Denied
                $("geoLocationInformation").set("text", gp_err_PremissionDenied);
                break;
            case 2: // Position unavailable
                $("geoLocationInformation").set("text", gp_err_PositionUnavailable);
                break;
            case 3: // Time out
                $("geoLocationInformation").set("text", gp_err_TimeOut);
                break;
            case 10: // Unsupported Browser
                $("geoLocationInformation").set("text", gp_err_UnsupportedBrowser);
                break;
            case 20: // Connection problems for AJAX
                $("geoLocationInformation").set("text", gp_err_NoConnection);
                break;
            default: // Unknown Error
                $("geoLocationInformation").set("text", gp_err_UnknownError);
                break;
        }
    }
});

var RunGeoProtection = new GeoProtection();

RunGeoProtection.setDebug(true);
RunGeoProtection.setCookie(gp_cookieEnabeld);
RunGeoProtection.setCookieLifetime(gp_cookieDurationW3C);

window.addEvent('domready', function(){
    RunGeoProtection.run(); 	
});



