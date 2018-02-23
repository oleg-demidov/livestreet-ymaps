jQuery(function($) { 
    var profileSettings = ls.registry.get('profile_settings');
    if(profileSettings !== undefined && profileSettings.enable){
        var profileData = ls.registry.get('ymapData');
        if( (typeof profileData === "object") && (profileData !== null) ){
            profileSettings = Object.assign(profileSettings, {data:profileData});
        }

        profileSettings = Object.assign(profileSettings, {
            i18n:{
                mark_on_map:ls.lang.get('plugin.ymaps.field.mark_on_map')
            }
        });
        
        $('.ls-field--geo').addClass('ymaps-geo-field').settingsMap(profileSettings);        
    }
    
});

