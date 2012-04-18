/**
 * GeoProtection Information Module
 * 
 * @copyright  MEN AT WORK 2012
 * @package    GeoProtection
 */

/**
 * Set choosen for dropdown 
 */
window.addEvent('domready', function(){
    $$(".geochange").chosen();
});

/**
 * Class GeoEdit
 *
 * Provide methods for update geoinformation
 * 
 * @copyright  MEN AT WORK 2012
 * @package    geolocation
 */
var GeoEdit = new Class({
    Implements: [Options],
    options:
    {
        debug: false,
        cookieLifeTime: 1,
        messages: {}
    },
    initialize: function(options){
        this.setOptions(options);
    },
    setCookieLifetime: function(lifetime)
    {
        this.options.cookieLifeTime = lifetime;
    }, 
    setMessages: function(messages)
    {
        options = {};
        options.messages = messages;
        this.setOptions(options);
    },
    changeGeoLocation: function (geoSelectId, geoInfoId, lang)
    {
        var value = $(geoSelectId).getSelected().get("text")[0];
        var valueShort = $(geoSelectId).getSelected().get("value")[0];        

        // Check if the request token is set
        if( typeof(REQUEST_TOKEN) !== 'undefined' )
        {
            data = {
                "action"        : "GeoChangeLocation",
                "location"      : valueShort,
                "REQUEST_TOKEN" : REQUEST_TOKEN
            }
        }
        else
        {
            data = {
                "action"        : "GeoChangeLocation",
                "location"      : valueShort
            }
        }

        // Start Progress
        if($(geoInfoId) != null)
        {
            $(geoInfoId).set("html", this.options.messages.geo_msc_Changing);
        }

        // Send new request
        new Request.JSON({
            method:'post',
            url: "ajax.php?language="+lang,
            data: data,
            evalScripts:false,
            evalResponse:false,
            onSuccess:function(json,responseElements){   
                // Update Request Token
                if( typeof(REQUEST_TOKEN) !== 'undefined' )
                {
                    REQUEST_TOKEN = json.token;
                }

                if(json.content.success == true)
                {             
                    var cookieValues = {
                        lat: json.content.lat, 
                        lon: json.content.lon,
                        countryShort: valueShort,
                        mode: 5
                    };

                    var cookieOption = {
                        duration: this.options.cookieLifeTime 
                    }

                    Cookie.write('Geolocation', JSON.encode(cookieValues), cookieOption);                

                    // Reload page
                    window.location.reload(); 
                }
                else
                {
                    if($(geoInfoId))
                    {
                        $(geoInfoId).set("html", json.content.error);
                    }
                }             
            }.bind(this),
            onFailure:function(json,responseElements){   
                if($(geoInfoId))
                {
                    $(geoInfoId).set("html", this.options.messages.geo_err_NoConnection);
                }           
            }.bind(this)
        }).send();
    }
});

var GeoUpdater = new GeoEdit();
