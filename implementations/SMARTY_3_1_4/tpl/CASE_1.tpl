<table>
	{section name=outer loop=$data}
		{foreach from=$data[outer] key=k item=v}
		  <tr>
			<td>{$k}</td>
			<td>{$v}</td>
		  </tr>
		{/foreach}
	{/section}
</table>
