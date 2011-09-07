Translated at http://OpenTranslators.org ~*~ Consider getting involved!


This README contains:

### I. Before you get started....

### II. Install com_jfoobar

### III. Customize Your Component

### IV. Learning more about developing Joomla Components

### V. Fork it and make it better!

### VI. Translate with Open Translators


## I. Before you get started....

### 1.  Prepare your development environment.

**a**. You’ll need an IDE, like Eclipse or phpStorm and a localhost environment. If you don't already have a localhost development environment, you could use the "Setting up your workstation for Joomla! development" article here: http://docs.joomla.org/Setting_up_your_workstation_for_Joomla!_development

**b**. Install Joomla 1.7 on a localhost development site.

### 2. Name your Component

**a**. Select a name for your Component that is a noun with both a singular and plural form. For example, foobar and foobars or item and items.

**b**. The Component name itself will be “com_” followed by the plural form.


## II. Install com_jfoobar and Review the "out of the box" Component functionality.

### 1. Click the download button https://github.com/Niambie/jfoobar

### 2. Install like any other Joomla Extension

### 3. Review the "out of the box" Component functionality.

**a**. Click the menu item 'Component-Your Component Name.' You will see the List View (sometimes called the Component Manager View), for your Component.

**b**. Click the Options button. There are three tabs: Component, Text Filtering and ACL.
    - You will learn to add parameters in this README.
    - Make certain to specify “No Filtering” for the Administrator or HTML will be stripped out during editing sessions.
    - Row-level ACL for your Component is already in place.
    - Press the 'Save' to process changes and close the panel.

**c**. Click the 'Category' submenu item.
    - Create a Category.
    - All of the Category to Component linkages are in place for your Component.

**d**. Create an Item for your Component.
    - Click the 'Component Name' submenu item to return to the List View.
    - Click the 'New' button.
    - The Administrator Form View is in place for your Component.
    - Make note of the Attributes slider. That is where your Custom Fields appear for your Component.

**e**. Use the List View for processing Component Items.
    - Return to the List View and test the buttons and filters to ensure all are working properly.
    - All of the display columns are sortable.

**f**. Create Site Menu Items.
    - Click the menu item 'Menus-Main Menu.'
    - Create three new Menu Items for your Component using the Item, List, and Form Menu Items.

**g**. Review the Site.
    - Go to the Frontend of your Website and review the Menu Items from the front end.
    - A basic “stub” is in place for your Component and three working Views that you can customize the Layouts, as needed.


## III. Customize Your Component

### 1. Review your Component Table 'jos_foobars' and the 'custom_fields' and 'parameters' columns. Data customizations are stored in those two fields, you will not have to change your database table to customize your component.

### 2. Change the 'custom_fields' column.

**a**. Open the administrator/components/com_jfoobar/models/forms/foobar.xml file using your IDE.

- The XML defines the data in your table to JForm, the Form API introduced in Joomla 1.6.
- Find the literal: <fields name="custom_fields">. That is the location that you can remove and add columns, as needed.
- Here is an example of a field you will find:

    <field
        name="link1"
        type="url"
        label="COM_FOOBARS_FIELD_LINK1_LABEL"
        description="COM_FOOBARS_FIELD_LINK1_DESC"
        class="inputbox"
        size="30"
    />

**b**. Create fields using JForm Fieldtypes. The list of fieldtypes in core is available on the Joomla Wiki: http://docs.joomla.org/Form_field

**c**. Although it's more advanced, you can create new JForm Custom Fieldtypes if the list above does not contain what you need. To learn to do so, see: http://docs.joomla.org/Creating_a_custom_form_field_type


### 3. Change Language Strings

**a**. Language files for your Component are located at administrator/components/com_jfoobar/language/xx-XX/xx-XX.com_releases.ini
https://github.com/Niambie/com_jfoobar/blob/master/administrator/components/com_jfoobar/language/en-GB/en-GB.com_jfoobar.ini

**b**. Note: the “sys” language file is used within the Administrator for Menu Items and other system displays.

**c**. If you want to change the values that appear as the labels and descriptions on forms, use the name field from the Form View (in the following example, subtitle is the name):

    <?php echo $this->form->getInput('subtitle'); ?>

- Then, look for the name=”subtitle” in the XML file identified in the previous step:

    <field
        name="subtitle"
        type="text"
        label="COM_FOOBARS_FIELD_SUBTITLE_LABEL"
        description="COM_FOOBARS_FIELD_SUBTITLE_DESC"
        class="inputbox"
        size="30"
    />

**d**. Search for the label and description values in the Language files and make the changes desired.


### 4. Change the View Layouts

**a**. The frontend View Layout are only a 'stub' to get you started. You will have to customize the Layouts, as needed.

**b**. For the Administrator, there are two Views:

- The List View is located at administrator/components/com_jfoobar/views/foobars/tmpl/default.php file.
https://github.com/Niambie/com_jfoobar/blob/master/administrator/components/com_jfoobar/views/foobars/tmpl/default.php

- The Edit View located at administrator/components/com_jfoobar/views/foobar/tmpl/edit.php file.
https://github.com/Niambie/com_jfoobar/blob/master/administrator/components/com_jfoobar/views/foobar/tmpl/edit.php

**c**. For the Site, there are three Views in the Component:

-  The List View is located at components/com_jfoobar/views/foobars/tmpl/default.php file.
https://github.com/Niambie/com_jfoobar/blob/master/components/com_jfoobar/views/foobars/tmpl/default.php

- The Item View located at components/com_jfoobar/views/foobar/tmpl/item.php file.
https://github.com/Niambie/com_jfoobar/blob/master/components/com_jfoobar/views/foobar/tmpl/default.php

- The Form View located at components/com_jfoobar/views/form/tmpl/edit.php file.
https://github.com/Niambie/com_jfoobar/blob/master/components/com_jfoobar/views/form/tmpl/edit.php


### 5. Change Administrator Component Title and Menu Item Icons and/or Modify CSS/JS

**a**. Change the Images in the media/com_jfoobar/images folder.

**b**. Update the CSS in the media/com_jfoobar/css/administrator.css file

**c**. The administrator.css is loaded in the Administrator List and Edit Views:
- administrator/components/com_jfoobar/views/foobars/tmpl/default.php file.
- administrator/components/com_jfoobar/views/foobar/tmpl/edit.php file.

More from the Wiki (although it's a bit dated) http://docs.joomla.org/How_to_create_a_custom_button


## IV. Learning more about developing Joomla Components

There is so much more that you can do, including adding and removing Views and Layouts. com_jfoobar is a good 'getting started' approach to learning how to create your own Components.

There is a wealth of great information on the Joomla Wiki. Here are some links to get started:

MVC http://docs.joomla.org/Developing_a_Model-View-Controller_%28MVC%29_Component_for_Joomla!1.6

Component Updates http://docs.joomla.org/Managing_Component_Updates_with_Joomla!1.6_-_Part_1



## V. Fork it and make it better!

**a**. Report issues here => https://github.com/Niambie/com_jfoobar/issues

**b**. Fork it and issue a pull request. Pass on your improvements to others. :)

**c**. Have fun!


## VI. Translate with Open Translators

http://OpenTranslators.org is a fabulous group of Joomla Translators who translate Joomla extensions. They welcome your involvement!
If you want to translate jFoobar into your language and share your translations with others, then THANKS! and please contact the http://OpenTranslators.org team to see how to get involved.