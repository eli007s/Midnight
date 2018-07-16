{!block "content"!}
<p>Hello, your a little about yourself</p>
<form action="/bio/create" method="post">
    <div class="form-group">
        <label for="fname">First Name</label>
        <input type="text" name="fname" id="fname" placeholder="First Name" />
    </div>

    <div class="form-group">
        <label for="lname">Last Name</label>
        <input type="text" name="lname" id="lname" placeholder="Last Name" />
    </div>

    <div class="form-group">
        <label for="bio">Bio</label>
        <textarea name="bio" id="bio"></textarea>
    </div>

    <div class="form-group">
        <input type="submit" value="Submit">
    </div>
</form>

<table>
    <thead>
    <tr>
        <td>Participants</td>
    </tr>
    </thead>
    <tbody>
    {!if $bio|is_array && $bio|count > 0!}
    {!foreach $bio as $k => $v!}
    <tr>
        <td><a href="/bio/show/{!$v.bio_id!}" target="_blank">{!$v.fname!} {!$v.lname!}</a></td>
    </tr>
    {!/foreach!}
    {!else!}
    <tr>

    </tr>
    {!/if!}
    </tbody>
</table>
{!/block!}