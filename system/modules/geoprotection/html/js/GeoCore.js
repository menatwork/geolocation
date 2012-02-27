/**
 * Class GeoProptection
 *
 * Provide methods for GeoProtection.
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
        lon: 0        
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
        this.options.cookieLifetime = lifetime;
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
            lon: this.vars.lon
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
                if(this.options.debug == true)
                {
                    console.log("Success");
                    console.log(responseElements);
                }
                
                this.onFailure();
            }.bind(this),
            onFailure:function(json,responseElements){                               
                if(this.options.debug == true)
                {
                    console.log("Error by Json Request");
                    console.log(responseElements);
                }
            }.bind(this)
        }).send();
    },
    updatePosition: function()
    {
        if(this.options.debug == true)
        {
            console.log("Start location updating");
            console.log(this.vars);
        }
        
        // Check if the request token is set
        if( typeof(REQUEST_TOKEN) !== 'undefined' )
        {
            data = {
                "action"    : "GeoProSetLocation",
                "lat"  : this.vars.lat,
                "lon"  : this.vars.lon,
                "REQUEST_TOKEN" : REQUEST_TOKEN
            }
        }
        else
        {
            data = {
                "action"    : "GeoProSetLocation",
                "lat"  : this.vars.lat,
                "lon"  : this.vars.lon
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
                if(json.content.success == true)
                {
                    this.updateCookie();     
                    this.onSuccess();
                }
                else if(json.success != true)
                {
                    this.removeCookie(); 
                    if(this.options.debug == true)
                    {
                        console.log("Error by server");
                        console.log(responseElements);
                    }
                }
            }.bind(this),
            onFailure:function(json,responseElements){
                this.removeCookie(); 
                
                if(this.options.debug == true)
                {
                    console.log("Error by Json Request");
                    console.log(responseElements);
                }
            }.bind(this)
        }).send();
    }, 
    onSuccess: function()
    {
        window.location.reload();
    },    
    onFailure: function()
    {
        
    }
});

var RunGeoProtection = new GeoProtection();
//RunGeoProtection.setDebug(true);
RunGeoProtection.setCookie(true);
RunGeoProtection.run();