{literal}
<script>
function drawChart(data) {
  //put d3 magic inside.
  console.log("look, a unicorn!");
}

melovedata = {/literal}{crmAPI entity="report_template" action="getrows"
report_id="contribute/recovery"
instance_id=36};
//{"is_error":0,"version":3,"count":2,"values":[{"from_date":"2010-01-01","to_date":"2011-07-01","recovered":780","prior":"508"},{"from_date":"2011-07-01","to_date":"2012-12-30","recovered":"999","prior":"845"}]}
{literal}
drawChart (melovedata.values);

</script>
{/literal}