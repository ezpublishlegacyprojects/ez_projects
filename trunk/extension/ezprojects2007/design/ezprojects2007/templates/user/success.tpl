<div class="template-design area-main-normal">
<div class="template-module user-success">
<div class="template-object">


{section show=$verify_user_email}
    <div class="attribute-heading">
        <h1>{"Welcome to the eZ Publish Community!"|i18n("design/standard/user")}</h1>
    </div>

    <div class="feedback">
        <p>
            Your account was successfully created. An e-mail will be sent to the specified
            e-mail address. You need to follow the instructions in that mail to activate
            your account.
        </p>
    </div>
    <a href={ezhttp( 'LastAccessesURI', 'session' )|ezurl}>{"Start your travel within the eZ Publish Community."|i18n("design/standard/user")}</a>
{section-else}
    <div class="maincontentheader">
        <h1>{"Welcome to the eZ Publish Community!"|i18n("design/standard/user")}</h1>
    </div>

    <div class="feedback">
        <h2>{"Your account was successfully created."|i18n("design/standard/user")}</h2>
        <p>
            {"Discover what being a member of the eZ Publish Community brings you, and how you can get involved: %get_involved_link."|i18n("design/standard/user",, hash( '%get_involved_link', '<a href="http://share.ez.no/get-involved">http://share.ez.no/get-involved</a>' ) )}
        </p>
    </div>
{/section}

</div>
</div>
</div>
