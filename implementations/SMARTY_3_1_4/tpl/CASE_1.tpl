{section name=outer loop=$data}
  <table>
	{foreach from=$data[outer] key=k item=v}
	  <tr>
		<td>{$k}</td>
		<td>{$v}</td>
	  </tr>
	{/foreach}
  </table>
{/section}
