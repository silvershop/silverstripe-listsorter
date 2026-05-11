<% if Sorter %>
	<p class="silvershop-sorter">
		<span class="silvershop-sorter__label">Sort:</span>
		<% loop Sorter.Sorts %>
			<span class="silvershop-sorter__option silvershop-sorter__option--$ID<% if IsCurrent %> silvershop-sorter__option--current<% end_if %>">
				<a class="silvershop-sorter__link" href="$Link" title="sort by $Title">$Title</a>
			</span>
			<% if not Last %><span class="silvershop-sorter__separator">|</span><% end_if %>
		<% end_loop %>
	</p>
<% end_if %>
