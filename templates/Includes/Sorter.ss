<% if Sorter %>
	<p class="sorter">
		<span class="sorter_label">Sort:</span>
		<% loop Sorter.Sorts %>
			<span class="sorter_option sorter_option_$ID<% if IsCurrent %> sorter_current<% end_if %>">
				<a href="$Link" title="sort by $Title">$Title</a>
			</span>
			<% if not Last %>|<% end_if %>
		<% end_loop %>
	</p>
<% end_if %>
