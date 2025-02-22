# Enable RDP (same as before)
# ...

# Get the password from Secret Manager (using the gcloud command inside the script)
$password = gcloud secrets versions access latest --secret="rdp-password" --project="your-gcp-project-id"  # Replace with your project ID
$password = ConvertTo-SecureString $password -AsSecureString -Force

# Create the RDP user (same as before, using the retrieved password)
$username = "rdpuser"
New-LocalUser -Name $username -Password $password -FullName "RDP User" -Description "User for RDP access"
Add-LocalGroupMember -Group "Administrators" -Member $username

# ... (other configurations)
