<?php
$file = 'C:\wamp64\www\7\resources\views\restaurant\purchasing\requests.blade.php';
$content = file_get_contents($file);

$actionTarget = <<<HTML
                            <a onclick="document.getElementById('viewDetailsModal_{{ \$purchaseRequest->id }}').classList.remove('hidden')" class="text-xs font-bold text-blue-600 hover:underline uppercase mr-3 cursor-pointer">View Details</a>
HTML;

$newActionTarget = <<<HTML
                            <a onclick="document.getElementById('viewDetailsModal_{{ \$purchaseRequest->id }}').classList.remove('hidden')" class="text-xs font-bold text-blue-600 hover:underline uppercase mr-3 cursor-pointer">View Details</a>
                            @if(\$purchaseRequest->status->value == 'draft')
                                <a href="{{ route('restaurant.purchasing.requests.edit', \$purchaseRequest->id) }}" class="text-xs font-bold text-amber-600 hover:underline uppercase mr-3">Edit</a>
                            @endif
HTML;

$content = str_replace($actionTarget, $newActionTarget, $content);

file_put_contents($file, $content);
echo "Added Edit button to UI.\n";
