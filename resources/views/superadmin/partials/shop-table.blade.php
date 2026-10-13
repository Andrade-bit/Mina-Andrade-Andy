<div class="table-wrap"><table><thead><tr><th>Shop</th><th>Owner</th><th>Plan</th><th>Status</th><th>Action</th></tr></thead><tbody>
@forelse($shops as $shop)
<tr><td>{{ $shop->name }}<br><span class="muted tiny">{{ $shop->slug }}</span></td><td>{{ $shop->users->firstWhere('role', 'owner')?->name ?? 'No owner assigned' }}</td><td>{{ $shop->plan->name }}</td><td><span class="badge {{ $shop->status !== 'active' ? $shop->status : $shop->subscription_status }}">{{ str($shop->status !== 'active' ? $shop->status : $shop->subscription_status)->replace('_', ' ')->title() }}</span></td><td><a class="btn secondary" href="{{ route('superadmin.shops.edit', $shop) }}">Manage →</a></td></tr>
@empty<tr><td colspan="5" class="muted">No shops match these filters.</td></tr>@endforelse
</tbody></table></div>
