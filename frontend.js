var byt = {
    players: []
}

jQuery(function($){
    
});

function onYouTubePlayerReady(playerId) {
      var $ = jQuery;
      
      playerId = unescape(playerId);
      
      byt.players[playerId] = document.getElementById(playerId);

      // We bind the event by delegation on the parent
      $( byt.players[playerId] ).parent( ).find('li a').click(function(e){

          var t = $(this);

          var id = t.attr('href').split('?v=')[1];
          
          //Change the video
          // Firts get hte player ID
          var player = t.parent( );
          
          while(!player.hasClass('youtube-widget')){
              player = player.parent( );
          }
          
          // Good parent has been found we check the id
          player = player.attr('id') + '_player';

          // Now get the real player
          player = byt.players[player];
          // Load the next video
          player.loadVideoById( id );
          
          return false;
      });

      byt.player.playVideo();
}
