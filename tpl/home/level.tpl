<?elements
class form action "home"
string map
string info
string time
string current_seed
link home lb "Home" route "home"
link test lb "Room test" route "home/room"
link play lb "Play" route "home/play"
select level list "1,city,2,cellars-1,3,cellars-2,4,cellars-3"
input seed hint "Nastavit seed"
button update lb "Nastavit"
?>
<style>
  .info { 
    position: fixed;
    right: 10px;
    top: 10px;

  }
</style>

<div class="p-3">{home} | {test} | {play}</div>
<div class="p-3">{seed} {level} {update} Seed: {current_seed}</div>

<div class="map">{map}</div>
<div class="info"></div> 

Elapsed time: {time} ms 

<script>
function loadInfo(x,y)
{
  $(".info").load("?r=home/info&x="+x+'&y='+y);
}
</script>