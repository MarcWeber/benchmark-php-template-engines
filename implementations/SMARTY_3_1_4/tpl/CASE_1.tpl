<table>
	
	{section name=outer loop=$data}
		{section name=key loop=$data[$outer]}
		
		  <tr>
			
			<td>{$key}</td>
			<td>{$data[$outer][$key]}</td>
			
		  </tr>
	
		{/section}
	{/section}
	
</table>
