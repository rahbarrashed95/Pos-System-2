	@foreach($ledger as $data)
		<tr>
			<td class="row-border">{{ $data['date'] }}</td>
			<td>{{$data['ref_no']}}</td>
			<td>{{$data['type']}}</td>
			<td>{{$data['location']}}</td>
			<td>{{$data['payment_status']}}</td>
			{{--<td class="ws-nowrap align-right">
			    @if($data['total'] !== '') 
			    @format_currency($data['total']) 
			    @endif</td>--}}
			<td class="ws-nowrap align-right">
			    @if($data['debit'] != '')
			       {{ $data['debit'] }}
			    @endif
			</td>
			<td class="ws-nowrap align-right">
			    @if($data['credit'] != '')
			       {{ $data['credit'] }}
			    @endif</td>
			<td class="ws-nowrap align-right">{{$data['balance']}}</td>
			<td>{{$data['payment_method']}}</td>
			<td>
				{!! $data['others'] !!}

				@if(!empty($is_admin) && !empty($data['transaction_id']) && $data['transaction_type'] == 'ledger_discount')
					<br>
					<button type="button" class="btn btn-xs btn-danger delete_ledger_discount" data-href="{{action([\App\Http\Controllers\LedgerDiscountController::class, 'destroy'], ['ledger_discount' => $data['transaction_id']])}}"><i class="fas fa-trash"></i></button>
					<button type="button" class="btn btn-xs btn-primary btn-modal" data-href="{{action([\App\Http\Controllers\LedgerDiscountController::class, 'edit'], ['ledger_discount' => $data['transaction_id']])}}" data-container="#edit_ledger_discount_modal"><i class="fas fa-edit"></i></button>
				@endif
			</td>
		</tr>
	@endforeach