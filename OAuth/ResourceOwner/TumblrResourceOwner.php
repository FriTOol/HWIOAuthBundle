<?php

namespace HWI\Bundle\OAuthBundle\OAuth\ResourceOwner;

use Symfony\Component\OptionsResolver\OptionsResolver;

/**
 * TwitterResourceOwner
 *
 * @author Alexander <iam.asm89@gmail.com>
 */
class TumblrResourceOwner extends GenericOAuth1ResourceOwner
{
    /**
     * {@inheritDoc}
     */
    protected $paths = array(
        'identifier'     => 'response.user.id_str',
        'nickname'       => 'response.user.name',
        'realname'       => 'response.user.name',
        'profilepicture' => 'profile_image_url_https',
    );

    /**
     * {@inheritDoc}
     */
    protected function configureOptions(OptionsResolver $resolver)
    {
        parent::configureOptions($resolver);

        $resolver->setDefaults(array(
            'authorization_url' => 'https://www.tumblr.com/oauth/authorize',
            'request_token_url' => 'https://www.tumblr.com/oauth/request_token',
            'access_token_url'  => 'https://www.tumblr.com/oauth/access_token',
            'infos_url'         => 'https://api.tumblr.com/v2/user/info',
        ));

        // Symfony <2.6 BC
        if (method_exists($resolver, 'setDefined')) {
            $resolver->setDefined('x_auth_access_type');
            // @link https://dev.twitter.com/oauth/reference/post/oauth/request_token
            $resolver->setAllowedValues('x_auth_access_type', array('read', 'write'));
        } else {
            $resolver->setOptional(array(
                'x_auth_access_type',
            ));
            $resolver->setAllowedValues(array(
                'x_auth_access_type' => array('read', 'write'),
            ));
        }
    }
}
