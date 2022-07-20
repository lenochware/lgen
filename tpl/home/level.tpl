<?elements
string map
string info
link home lb "Home" route "home"
link test lb "Room test" route "home/test"
?>
{home} | {test}

<div class="flex">
	<div class="map">{map}</div>
	<div class="info"></div>	
</div>

<script>
function loadInfo(x,y)
{
	$(".info").load("?r=home/info&x="+x+'&y='+y);
}
</script>