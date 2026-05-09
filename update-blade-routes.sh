#!/bin/bash

# Script to update hardcoded siswa/guru routes to dynamic role-aware routes
# Usage: bash update-blade-routes.sh

echo "Updating Blade views with dynamic role routes..."

# Function to update a single file
update_file() {
    local file=$1
    echo "Processing: $file"

    # Backup original file
    cp "$file" "$file.bak"

    # Replace siswa routes with parameters
    sed -i "s/route('siswa\.\([^']*\)',/route(auth()->user()->getRolePrefix() . '.\1',/g" "$file"

    # Replace guru routes with parameters
    sed -i "s/route('guru\.\([^']*\)',/route(auth()->user()->getRolePrefix() . '.\1',/g" "$file"

    echo "✓ Updated: $file"
}

# Update siswa views
echo "=== Updating Siswa Views ==="
find resources/views/siswa -name "*.blade.php" -type f | while read file; do
    if grep -q "route('siswa\." "$file"; then
        update_file "$file"
    fi
done

# Update guru views
echo "=== Updating Guru Views ==="
find resources/views/guru -name "*.blade.php" -type f | while read file; do
    if grep -q "route('guru\." "$file"; then
        update_file "$file"
    fi
done

echo ""
echo "✓ All files updated!"
echo "Backup files created with .bak extension"
echo ""
echo "To restore backups if needed:"
echo "  find resources/views -name '*.blade.php.bak' -exec bash -c 'mv \"\$0\" \"\${0%.bak}\"' {} \;"
