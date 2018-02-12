jQuery(function($) { 
    var profileSettings = ls.registry.get('profile_settings');
    if(profileSettings !== undefined && profileSettings.enable){
        var profileData = ls.registry.get('ymapData');
        if( (typeof profileData === "object") && (profileData !== null) ){
            profileSettings = Object.assign(profileSettings, {data:profileData});
        }
        
        $('.ls-field--geo').addClass('ymaps-geo-field').settingsMap(profileSettings);        
    }
    
});

