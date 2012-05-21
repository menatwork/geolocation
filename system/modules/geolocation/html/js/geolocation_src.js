/**
 * Geolocation Information Module
 * 
 * @copyright  MEN AT WORK 2012
 * @package    geolocation
 */

/**
 * Set choosen for dropdown 
 */
window.addEvent('domready', function(){
    if(typeof chosen == 'function')
    {
        $$(".geochange").chosen();
    }
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
        messages: {},
        requesttoken: "",
        session: ""
    },
    initialize: function(options){
        this.setOptions(options); 
    }, 
    setMessages: function(messages)
    {
        options = {};
        options.messages = messages;
        this.setOptions(options);
    },
    setRequestToken: function(requesttoken)
    {
        options = {};
        options.requesttoken = requesttoken;
        this.setOptions(options);
    },
    setSession: function(session)
    {
        options = {};
        options.session = session;
        this.setOptions(options);
    },
    changeGeoLocation: function (geoSelectId, geoInfoId, lang)
    {       
        var value = $(geoSelectId).getSelected().get("text")[0];
        var valueShort = $(geoSelectId).getSelected().get("value")[0];        

        // Check if the request token is set
        if( this.options.requesttoken != "" )
        {
            data = {
                "action"        : "GeoChangeLocation",
                "session"       : this.options.session,
                "location"      : valueShort,
                "REQUEST_TOKEN" : this.options.requesttoken
            }
        }
        else
        {
            data = {
                "action"        : "GeoChangeLocation",
                "session"       : this.options.session,
                "location"      : valueShort
            }
        }

        // Start Progress
        if($(geoInfoId) != null)
        {
            $(geoInfoId).set("html", this.options.messages.changing);
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
                if( this.options.requesttoken )
                {
                    this.options.requesttoken = json.token;
                }

                if(json.content.success == true)
                {    
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
                    $(geoInfoId).set("html", this.options.messages.noConnection);
                }           
            }.bind(this)
        }).send();
    }
});

var GeoUpdater = new GeoEdit();
