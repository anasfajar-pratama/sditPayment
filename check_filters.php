<?php
$ref = new ReflectionProperty("Filament\Tables\Concerns\HasFilters", "tableFilters");
echo "Access: " . ($ref->isPublic() ? "public" : ($ref->isProtected() ? "protected" : "private")) . PHP_EOL;
