{literal}
<script>
function drawChart(data) {
  //put d3 magic inside.
  console.log("look, a unicorn!");
}

melovedata = {/literal}{crmAPI entity="report_template" action="getrows"
report_id="contribute/recovery"
instance_id=36};
{literal}
drawChart (melovedata.values);

</script>
{/literal}