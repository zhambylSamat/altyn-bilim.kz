<style type="text/css">
  
  .timer-text {
    font-size:23px;
    color: #C7012E;
    /*font-weight: bold;*/
    font-style: italic;
  }
  #timer, #timerForBaga {
    font-size: 23px;
    font-weight: bold;
    color: #C7012E;
  }
  #timer-container {
    /*border: 1px solid #324DA4;*/
    width: 100%;
    padding: 10px 0;
    /*background-color: #FB654E; */
    /*#C7012E*/
  }
  #timer-parent-container {
    padding: 0;
  }
</style>
<div class='container-fluid'>
  <div class='row'>
    <div class='col-md-12 col-sm-12 col-xs-12' id='timer-parent-container'>
      <div id='timer-container' class='pull-right'>
        <center>
          <span class='timer-text'>ҰБТ-ға дейін: </span> <span id='timer'></span>
        </center>
      </div>
    </div>
  </div>
</div>    

<script>
// Set the date we're counting down to
var countDownDate = new Date("Jun 20, 2020 09:00:0").getTime();
// Update the count down every 1 second

var x = setInterval(function() {
  setHtml("timer", get_time(countDownDate));
}, 60000);

function get_time(countDownDate) {
  // Get todays date and time
  var now = new Date().getTime();
    
  // Find the distance between now and the count down date
  var distance = countDownDate - now;
    
  // Time calculations for days, hours, minutes and seconds
  var days = Math.floor(distance / (1000 * 60 * 60 * 24));
  var hours = Math.floor((distance % (1000 * 60 * 60 * 24)) / (1000 * 60 * 60));
  var minutes = Math.floor((distance % (1000 * 60 * 60)) / (1000 * 60));
  var seconds = Math.floor((distance % (1000 * 60)) / 1000);
  var mileseconds = Math.floor((distance % (1000)) / 10);
  // If the count down is over, write some text 
  if (distance < 0) {
    clearInterval(x);
    return "ҰБТ-да сәттілік!!!";
    document.getElementById("timer").innerHTML = "ҰБТ-да сәттілік!!!";
  }
  return days + "күн " + hours + "сағ. ";
}
function setHtml(id, html) {
  document.getElementById(id).innerHTML = html;
}
setHtml("timer", get_time(countDownDate));
</script>