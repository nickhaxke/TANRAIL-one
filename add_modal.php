<?php
$file = 'C:\wamp64\www\7\resources\views\restaurant\purchasing\requests.blade.php';
$content = file_get_contents($file);

$modalHtml = <<<HTML
        <!-- View Details Modal -->
        <div id="viewDetailsModal_{{ \$purchaseRequest->id }}" class="hidden fixed inset-0 z-50 overflow-y-auto bg-gray-900/50 backdrop-blur-sm flex items-center justify-center p-4">
            <div class="bg-white border border-gray-300 rounded-sm max-w-2xl w-full p-0 shadow-2xl">
                <div class="flex items-center justify-between p-6 border-b border-gray-200 bg-gray-50">
                    <div>
                        <h3 class="text-lg font-black text-gray-900 uppercase tracking-widest">Purchase Request Details</h3>
                        <p class="text-xs font-bold text-gray-500 uppercase mt-1">PO Number: {{ \$purchaseRequest->reference_number }}</p>
                    </div>
                    <button type="button" onclick="document.getElementById('viewDetailsModal_{{ \$purchaseRequest->id }}').classList.add('hidden')" class="text-gray-400 hover:text-gray-900 text-2xl font-bold transition-colors">&times;</button>
                </div>
                
                <div class="p-6">
                    <div class="mb-4">
                        <p class="text-xs font-bold text-gray-500 uppercase">Supplier</p>
                        <p class="text-sm font-bold text-gray-900">{{ \$purchaseRequest->supplier->name ?? 'Unknown Supplier' }}</p>
                    </div>
                    
                    <div class="border border-gray-200 rounded-sm overflow-hidden mb-6">
                        <table class="w-full text-sm text-left">
                            <thead class="bg-gray-50 border-b border-gray-200">
                                <tr>
                                    <th class="py-2 px-3 font-bold text-gray-600 uppercase text-[10px] tracking-wider">Item</th>
                                    <th class="py-2 px-3 font-bold text-gray-600 uppercase text-[10px] tracking-wider text-right">Qty</th>
                                    <th class="py-2 px-3 font-bold text-gray-600 uppercase text-[10px] tracking-wider text-right">Unit Price</th>
                                    <th class="py-2 px-3 font-bold text-gray-600 uppercase text-[10px] tracking-wider text-right">Total</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-gray-100">
                                @forelse(\$purchaseRequest->lines as \$line)
                                <tr>
                                    <td class="py-2 px-3 font-bold text-gray-900">{{ \$line->item->name ?? 'Unknown' }}</td>
                                    <td class="py-2 px-3 text-right font-mono">{{ number_format(\$line->quantity, 1) }}</td>
                                    <td class="py-2 px-3 text-right font-mono">{{ number_format(\$line->unit_price, 2) }}</td>
                                    <td class="py-2 px-3 text-right font-mono text-gray-900 font-bold">{{ number_format(\$line->total, 2) }}</td>
                                </tr>
                                @empty
                                <tr>
                                    <td colspan="4" class="py-4 text-center text-gray-500">No items on this request.</td>
                                </tr>
                                @endforelse
                            </tbody>
                            <tfoot class="bg-gray-50 border-t border-gray-200">
                                <tr>
                                    <td colspan="3" class="py-2 px-3 text-right font-bold text-gray-900 uppercase tracking-widest text-[10px]">Grand Total</td>
                                    <td class="py-2 px-3 text-right font-mono font-black text-gray-900">{{ number_format(\$purchaseRequest->total, 2) }}</td>
                                </tr>
                            </tfoot>
                        </table>
                    </div>

                    <div class="flex justify-end gap-3 pt-4 border-t border-gray-200">
                        <button type="button" onclick="document.getElementById('viewDetailsModal_{{ \$purchaseRequest->id }}').classList.add('hidden')" class="px-4 py-2 border border-gray-300 text-xs font-bold uppercase text-gray-700 hover:bg-gray-50 rounded-sm">
                            Close
                        </button>
                        
                        @if(\$purchaseRequest->status->value === 'draft')
                        <form method="POST" action="{{ route('restaurant.purchasing.submit-draft', \$purchaseRequest->id) }}" class="inline">
                            @csrf
                            <button type="submit" class="px-5 py-2 bg-gray-900 text-white font-bold text-xs uppercase tracking-wider rounded-sm hover:bg-gray-800">
                                Submit for Approval &rarr;
                            </button>
                        </form>
                        @endif
                    </div>
                </div>
            </div>
        </div>
HTML;

$searchStr = "        @if(\$purchaseRequest->status->value == 'approved' || \$purchaseRequest->status->value == 'partially_received')";
$content = str_replace($searchStr, $modalHtml . "\n" . $searchStr, $content);
$content = str_replace("href=\"#\" class=\"text-xs font-bold text-blue-600 hover:underline uppercase mr-3\">View Details", "onclick=\"document.getElementById('viewDetailsModal_{{ \$purchaseRequest->id }}').classList.remove('hidden')\" class=\"text-xs font-bold text-blue-600 hover:underline uppercase mr-3 cursor-pointer\">View Details", $content);

file_put_contents($file, $content);
echo "Modal inserted.\n";
