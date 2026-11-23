@extends('admin.admin_app')

@section('content')
<div class="content-page">
    <div class="content">
        <div class="container-fluid">
            <div class="row"><div class="col-sm-12"><h4 class="page-title">Affiliate Program</h4></div></div>
            @if(session('affiliate_message')) <div class="alert alert-success">{{ session('affiliate_message') }}</div> @endif

            <div class="row">
                <div class="col-md-3"><div class="card-box"><h4>Affiliate accounts</h4><h2>{{ $stats['accounts'] }}</h2></div></div>
                <div class="col-md-3"><div class="card-box"><h4>Total referrals</h4><h2>{{ $stats['referrals'] }}</h2></div></div>
                <div class="col-md-3"><div class="card-box"><h4>Pending commissions</h4><h2>{{ getcong('currency_code') ?: 'USD' }} {{ number_format((float) $stats['pending_commissions'], 2) }}</h2></div></div>
                <div class="col-md-3"><div class="card-box"><h4>Requested payouts</h4><h2>{{ getcong('currency_code') ?: 'USD' }} {{ number_format((float) $stats['requested_payouts'], 2) }}</h2></div></div>
            </div>

            <div class="row"><div class="col-md-12"><div class="card-box">
                <h4>Program settings</h4>
                <form method="POST" action="{{ route('admin.affiliate.settings') }}">
                    @csrf
                    <div class="row">
                        <div class="col-md-6"><div class="form-group"><label>Landing title</label><input name="landing_title" class="form-control" value="{{ $settings->landing_title }}" required></div></div>
                        <div class="col-md-3"><div class="form-group"><label>Recurring commission %</label><input name="recurring_commission_percent" class="form-control" type="number" min="0" max="100" step="0.01" value="{{ $settings->recurring_commission_percent }}" required></div></div>
                        <div class="col-md-3"><div class="form-group"><label>Max referrals per cycle</label><input name="max_referrals_per_cycle" class="form-control" type="number" min="1" value="{{ $settings->max_referrals_per_cycle }}" required></div></div>
                    </div>
                    <div class="row">
                        <div class="col-md-6"><div class="form-group"><label>Description</label><textarea name="landing_description" class="form-control" rows="3">{{ $settings->landing_description }}</textarea></div></div>
                        <div class="col-md-3"><div class="form-group"><label>Minimum payout</label><input name="minimum_payout_amount" class="form-control" type="number" min="0" step="0.01" value="{{ $settings->minimum_payout_amount }}" required></div></div>
                        <div class="col-md-3"><div class="form-group"><label>Payout instructions</label><textarea name="payout_instructions" class="form-control" rows="3">{{ $settings->payout_instructions }}</textarea></div></div>
                    </div>
                    <label><input type="checkbox" name="enabled" value="1" {{ $settings->enabled ? 'checked' : '' }}> Program enabled</label>
                    <label style="margin-left:20px"><input type="checkbox" name="reset_on_payout" value="1" {{ $settings->reset_on_payout ? 'checked' : '' }}> Reset cycle after payout</label>
                    <br><button class="btn btn-primary" type="submit">Save settings</button>
                </form>
            </div></div></div>

            <div class="row"><div class="col-md-12"><div class="card-box">
                <h4>Commission tiers</h4>
                <form method="POST" action="{{ route('admin.affiliate.tiers.store') }}" class="form-inline">
                    @csrf
                    <input name="name" class="form-control" placeholder="Tier name" required>
                    <input name="min_referrals" class="form-control" type="number" min="1" placeholder="Min" required>
                    <input name="max_referrals" class="form-control" type="number" min="1" placeholder="Max">
                    <input name="commission_percent" class="form-control" type="number" min="0" max="100" step="0.01" placeholder="Rate %" required>
                    <input name="sort_order" class="form-control" type="number" min="0" value="0" placeholder="Order" required>
                    <label><input type="checkbox" name="active" value="1" checked> Active</label>
                    <button class="btn btn-primary" type="submit">Add tier</button>
                </form>
                <div class="table-responsive"><table class="table"><thead><tr><th>Name</th><th>Range</th><th>Rate</th><th>Status</th><th>Actions</th></tr></thead><tbody>
                @forelse($tiers as $tier)
                    <tr>
                        <form method="POST" action="{{ route('admin.affiliate.tiers.update', $tier->id) }}">
                            @csrf
                            <td><input name="name" class="form-control" value="{{ $tier->name }}"></td>
                            <td><input name="min_referrals" class="form-control" type="number" min="1" value="{{ $tier->min_referrals }}"><input name="max_referrals" class="form-control" type="number" min="1" value="{{ $tier->max_referrals }}"></td>
                            <td><input name="commission_percent" class="form-control" type="number" min="0" max="100" step="0.01" value="{{ $tier->commission_percent }}"></td>
                            <td><input type="hidden" name="sort_order" value="{{ $tier->sort_order }}"><label><input type="checkbox" name="active" value="1" {{ $tier->active ? 'checked' : '' }}> Active</label></td>
                            <td><button class="btn btn-sm btn-primary" type="submit">Save</button></form>
                                <form method="POST" action="{{ route('admin.affiliate.tiers.delete', $tier->id) }}" style="display:inline">@csrf <button class="btn btn-sm btn-danger" type="submit">Delete</button></form>
                            </td>
                    </tr>
                @empty <tr><td colspan="5">No tiers configured.</td></tr> @endforelse
                </tbody></table></div>
            </div></div></div>

            <div class="row"><div class="col-md-12"><div class="card-box">
                <h4>Commission review</h4>
                <div class="table-responsive"><table class="table"><thead><tr><th>Affiliate</th><th>Member</th><th>Type</th><th>Amount</th><th>Status</th><th>Action</th></tr></thead><tbody>
                @forelse($commissions as $commission)
                    <tr><td>{{ optional(optional($commission->account)->user)->email }}</td><td>{{ optional($commission->user)->email }}</td><td>{{ $commission->type }}</td><td>{{ $commission->currency }} {{ $commission->commission_amount }}</td><td>{{ $commission->status }}</td><td>
                        @if($commission->status === 'pending')
                            <form method="POST" action="{{ route('admin.affiliate.commissions.approve', $commission->id) }}" style="display:inline">@csrf<button class="btn btn-sm btn-success">Approve</button></form>
                            <form method="POST" action="{{ route('admin.affiliate.commissions.reject', $commission->id) }}" style="display:inline">@csrf<button class="btn btn-sm btn-danger">Reject</button></form>
                        @endif
                    </td></tr>
                @empty <tr><td colspan="6">No commissions yet.</td></tr> @endforelse
                </tbody></table></div>
            </div></div></div>

            <div class="row"><div class="col-md-12"><div class="card-box">
                <h4>Payout requests</h4>
                <div class="table-responsive"><table class="table"><thead><tr><th>Affiliate</th><th>Amount</th><th>Method</th><th>Details</th><th>Status</th><th>Action</th></tr></thead><tbody>
                @forelse($payouts as $payout)
                    <tr><td>{{ optional(optional($payout->account)->user)->email }}</td><td>{{ $payout->currency }} {{ $payout->amount }}</td><td>{{ $payout->method }}</td><td>{{ $payout->account_details }}</td><td>{{ $payout->status }}</td><td>
                        @if(in_array($payout->status, ['requested', 'approved']))
                            @if($payout->status === 'requested') <form method="POST" action="{{ route('admin.affiliate.payouts.approve', $payout->id) }}" style="display:inline">@csrf<button class="btn btn-sm btn-success">Approve</button></form> @endif
                            <form method="POST" action="{{ route('admin.affiliate.payouts.paid', $payout->id) }}" style="display:inline">@csrf<button class="btn btn-sm btn-primary">Mark paid</button></form>
                            <form method="POST" action="{{ route('admin.affiliate.payouts.reject', $payout->id) }}" style="display:inline">@csrf<button class="btn btn-sm btn-danger">Reject</button></form>
                        @endif
                    </td></tr>
                @empty <tr><td colspan="6">No payout requests yet.</td></tr> @endforelse
                </tbody></table></div>
            </div></div></div>
        </div>
    </div>
</div>
@endsection
