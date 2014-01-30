<% if Sorter %>
	<p class="sorter">
		<span calss="sorter_label">Sort By:</span>
		<% loop Sorter.Sorts %>
			<span class="sorter_option sorter_direction_$Direction">
				<a href="$Link" title="sort by $Title">$Title</a>
			</span>
			<% if not Last %>|<% end_if %>
		<% end_loop %>
	</p>
<% end_if %>