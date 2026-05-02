<% if Sorter %>
	<p class="silvershop-sorter">
		<span class="silvershop-sorter_label">Sort:</span>
		<% loop Sorter.Sorts %>
			<span class="silvershop-sorter_option silvershop-sorter_option_$ID<% if IsCurrent %> silvershop-sorter_current<% end_if %>">
				<a href="$Link" title="sort by $Title">$Title</a>
			</span>
			<% if not Last %>|<% end_if %>
		<% end_loop %>
	</p>
<% end_if %>
