# SugarCRM Lead Auto-Conversion

This SugarCRM module automates the lead conversion process, automatically creating Accounts, Contacts, and Opportunities with a single click.

## Features

- Automatic conversion of Lead to Account, Contact, and Opportunity
- Custom "Convert Lead" field added to the Lead module
- Automatic creation of relationships between new records
- Easy installation via SugarCRM's Module Loader

## Requirements

- SugarCRM 12.0 or higher
- Administrator permissions to install custom modules

## Installation

1. Download the `lead_conversion.zip` file from this repository.
2. Log into your SugarCRM instance as an administrator.
3. Navigate to Admin > Module Loader.
4. Click on "Upload file" and select the `lead_conversion.zip` file.
5. Click "Install" for the uploaded module.
6. Follow the on-screen instructions to complete the installation.

## Usage

1. Navigate to an existing Lead record or create a new one.
2. Locate the new "Convert Lead" field (typically a checkbox) on the Lead record.
3. Check the "Convert Lead" box.
4. Save the Lead record.
5. The system will automatically create a new Account, Contact, and Opportunity based on the Lead information.
6. The new records will be related to each other and to the original Lead.

## Troubleshooting

If you encounter issues during installation or use of this module:

1. Verify that you have the necessary permissions in SugarCRM.
2. Ensure your SugarCRM version is compatible (12.0 or higher).
3. Check the SugarCRM logs for detailed error messages.
4. If the problem persists, please open an issue in this GitHub repository.

## Contributing

Contributions to this project are welcome. Please open an issue to discuss proposed changes or submit a pull request with your improvements.

## Support

For support, please open an issue in this GitHub repository or contact your SugarCRM administrator.

## License

[Specify your license here, e.g., MIT, GPL, etc.]
