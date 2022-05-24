Changelog
=========

1.4.0 (May 24, 2022)
----------------------
- Enh: Adding a Token Authentication
- Enh: Adding an iCal interface
- Fix: (SECURITY) Users who should not have access can still access some information.
- Fix #2: If there are special characters in the user name, unexpected errors may occur. Unfortunately, all old URLs become invalid with this fix.
- Enh: On iOS devices, groups can be activated/deactivated (contacts can thus be displayed multiple times).
- Fix: Sabre/dav uses the deprecated libxml_disable_entity_loader function, which is no longer usable as of PHP version 8.0.

1.3.3 (May 14, 2022)
----------------------
- Fix: Removal of the problems related to the removal of the obsolete "Directory" module (since HumHub 1.11).

1.3.2 (October 28, 2021)
---------------------
- Enh: This module is now available in HumHum Marketplace.
- Chng: Removal of the update mechanism.