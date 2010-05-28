

<script>
$("a#qq").click(function(){
	var id = $(this).attr('alt');
	var data = $(this).attr('value');
	var username = $(this).attr('name');
			
	// start the html part
	data = "[quote=" + username + "]" + $.trim(data) + "[/quote]";
		
	$("#qcontent").val($("#qcontent").val() + $.trim(data));
		
	$.scrollTo('#qr', 800);
});
</script>

</body>
</html>