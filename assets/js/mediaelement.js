jQuery(document).ready(function($) {
    // Initialize MediaElement.js with our custom settings
    $('.wpfc-sermon-player, .wpfc-sermon-video-player').each(function() {
        var player = new MediaElementPlayer(this, {
            // Custom configuration for our plugin
            features: ['playpause', 'current', 'progress', 'duration', 'volume', 'fullscreen'],
            stretching: 'responsive',
            enableAutosize: true,
            success: function(mediaElement, originalNode, instance) {
                // Handle seek functionality if present
                if (mediaElement.dataset.plyr_seek !== undefined) {
                    mediaElement.setCurrentTime(parseInt(mediaElement.dataset.plyr_seek));
                }
            }
        });
    });
}); 