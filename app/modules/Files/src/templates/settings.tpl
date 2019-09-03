{* todo - add option to reset template to default *}
<div class="ui grid">
    <div class="one column row">
        <div class="column">
            {viewCrumbtrail(array("/cms/blog/overview/{$blog->id}", {$blog->name}, "/cms/settings/menu/{$blog->id}", 'Settings'), 'Files')}
        </div>
    </div>
    <div class="one column row">
        <div class="column">
            {viewPageHeader('File settings', 'sliders horizontal', {$blog->name})}
        </div>
    </div>
    <div class="one column row">
        <div class="column">
            <h2>Image sizes</h2>
            <div class="ui icon info message">
                <i class="info circle icon"></i>
                <div class="content">When uploading an image to Blog CMS, several different versions are saved automatically. These are smaller images, designed to be used on pages which list multiple posts, to save the end-user from having to download large image files.</div>
            </div>
            <form method="POST" class="ui form" id="post_settings_form">
                <div class="ui grid">
                    <div class="two columns row">
                        <div class="column">
                            <h3>XL</h3>
                            <div class="field">
                                <label for="xl_image_width">Width (px)</label>
                                <input type="number" id="xl_image_width" name="xl_image_width" value="{$config.imagestyles.xl.w}">
                            </div>
                        </div>
                        <div class="column">
                            <h3>&nbsp;</h3>
                            <div class="field">
                                <label for="xl_image_height">Height (px)</label>
                                <input type="number" id="xl_image_height" name="xl_image_height" value="{$config.imagestyles.xl.h}">
                            </div>
                        </div>
                    </div>
                    <div class="two columns row">
                        <div class="column">
                            <h3>L</h3>
                            <div class="field">
                                <label for="l_image_width">Width (px)</label>
                                <input type="number" id="l_image_width" name="l_image_width" value="{$config.imagestyles.l.w}">
                            </div>
                        </div>
                        <div class="column">
                            <h3>&nbsp;</h3>
                            <div class="field">
                                <label for="l_image_height">Height (px)</label>
                                <input type="number" id="l_image_height" name="l_image_height" value="{$config.imagestyles.l.h}">
                            </div>
                        </div>
                    </div>
                    <div class="two columns row">
                        <div class="column">
                            <h3>M</h3>
                            <div class="field">
                                <label for="m_image_width">Width (px)</label>
                                <input type="number" id="m_image_width" name="m_image_width" value="{$config.imagestyles.m.w}">
                            </div>
                        </div>
                        <div class="column">
                            <h3>&nbsp;</h3>
                            <div class="field">
                                <label for="m_image_height">Height (px)</label>
                                <input type="number" id="m_image_height" name="m_image_height" value="{$config.imagestyles.m.h}">
                            </div>
                        </div>
                    </div>
                    <div class="two columns row">
                        <div class="column">
                            <h3>S</h3>
                            <div class="field">
                                <label for="s_image_width">Width (px)</label>
                                <input type="number" id="s_image_width" name="s_image_width" value="{$config.imagestyles.s.w}">
                            </div>
                        </div>
                        <div class="column">
                            <h3>&nbsp;</h3>
                            <div class="field">
                                <label for="s_image_height">Height (px)</label>
                                <input type="number" id="s_image_height" name="s_image_height" value="{$config.imagestyles.s.h}">
                            </div>
                        </div>
                    </div>
                    <div class="two columns row">
                        <div class="column">
                            <h3>XS</h3>
                            <div class="field">
                                <label for="xs_image_width">Width (px)</label>
                                <input type="number" id="xs_image_width" name="xs_image_width" value="{$config.imagestyles.xs.w}">
                            </div>
                        </div>
                        <div class="column">
                            <h3>&nbsp;</h3>
                            <div class="field">
                                <label for="xs_image_height">Height (px)</label>
                                <input type="number" id="xs_image_height" name="xs_image_height" value="{$config.imagestyles.xs.h}">
                            </div>
                        </div>
                    </div>
                    <div class="two columns row">
                        <div class="column">
                            <h3>Square</h3>
                            <div class="field">
                                <label for="sq_image_width">Width/height (px)</label>
                                <input type="number" id="sq_image_width" name="sq_image_width" value="{$config.imagestyles.sq.w}">
                            </div>
                        </div>
                        <div class="column"></div>
                    </div>
                    <div class="one column row">
                        <button class="ui teal button" type="submit">Save</button>
                    </div>
                </div>
            </form>

        </div>
    </div>
</div>