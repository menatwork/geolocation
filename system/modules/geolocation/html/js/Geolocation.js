/**
 * GeoProtection Information Module
 * 
 * @copyright  MEN AT WORK 2011-2012
 * @package    GeoProtection
 */

/**
 * Set choosen for dropdown 
 */
window.addEvent('domready', function(){
    if($("changeGeoLocation") != null)
    {
        var _myChosen = new Chosen($("changeGeoLocation"));
    }
});
    
/**
* Change the location by user settings
*/
function changeGeoLocation()
{
    var value = $("changeGeoLocation").getSelected().get("text")[0];
    var valueShort = $("changeGeoLocation").getSelected().get("value")[0];        
        
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
    if($("geoLocationInformation") != null)
    {
        $("geoLocationInformation").set("html", geo_msc_Changing);
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
            
            if(json.content.success == true)
            {             
                var cookieValues = {
                    lat: json.content.lat, 
                    lon: json.content.lon,
                    countryShort: valueShort,
                    mode: 5
                };
        
                var cookieOption = {
                    duration: geo_cookieDurationUser 
                }
                
                Cookie.write('Geolocation', JSON.encode(cookieValues), cookieOption);                
                
                // Reload page
                window.location.reload(); 
            }
            else
            {
                if($("geoLocationInformation"))
                {
                    $("geoLocationInformation").set("html", json.content.error);
                }
            }             
        }.bind(this),
        onFailure:function(json,responseElements){   
            if($("geoLocationInformation"))
            {
                $("geoLocationInformation").set("html", geo_err_NoConnection);
            }           
        }.bind(this)
    }).send();
}