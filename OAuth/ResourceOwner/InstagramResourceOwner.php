<?php

/*
 * This file is part of the HWIOAuthBundle package.
 *
 * (c) Hardware.Info <opensource@hardware.info>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace HWI\Bundle\OAuthBundle\OAuth\ResourceOwner;

use Symfony\Component\OptionsResolver\OptionsResolver;
use Buzz\Message\RequestInterface as HttpRequestInterface;

/**
 * InstagramResourceOwner
 *
 * @author Jean-Christophe Cuvelier <jcc@atomseeds.com>
 */
class InstagramResourceOwner extends GenericOAuth2ResourceOwner
{
    /**
     * {@inheritDoc}
     */
    protected $paths = array(
        'identifier'      => 'data.id',
        'nickname'        => 'data.username',
        'realname'        => 'data.full_name',
        'profilepicture'  => 'data.profile_picture',
    );

    public function getAuthorizationUrl($redirectUri, array $extraParameters = array())
    {
        if ($this->options['csrf']) {
            if (null === $this->state) {
                $this->state = $this->generateNonce();
            }

            $this->storage->save($this, $this->state, 'csrf_state');
        }

        $parameters = array_merge(array(
            'response_type' => 'code',
            'client_id'     => $this->options['client_id'],
            'scope'         => $this->options['scope'],
            'state'         => $this->state ? urlencode($this->state) : null,
            'redirect_uri'  => $redirectUri,
        ), $extraParameters);

        return str_replace('%2B', '+', $this->normalizeUrl($this->options['authorization_url'], $parameters));
    }
    
    /**
     * {@inheritDoc}
     */
    protected function doGetUserInformationRequest($url, array $parameters = array())
    {
        return $this->httpRequest($this->normalizeUrl($url, $parameters), null, array(), HttpRequestInterface::METHOD_GET);
    }

    /**
     * {@inheritDoc}
     */
    protected function configureOptions(OptionsResolver $resolver)
    {
        parent::configureOptions($resolver);

        $resolver->setDefaults(array(
            'authorization_url'         => 'https://api.instagram.com/oauth/authorize',
            'access_token_url'          => 'https://api.instagram.com/oauth/access_token',
            'infos_url'                 => 'https://api.instagram.com/v1/users/self',

            // Instagram supports authentication with only one defined URL
            'auth_with_one_url'        => true,

            'use_bearer_authorization' => false,
        ));
    }
}
