/*
 * This file is part of the Sententiaregum project.
 *
 * (c) Maximilian Bosch <maximilian@mbosch.me>
 * (c) Ben Bieler <ben@benbieler.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

'use strict';

export default {
  menu: {
    start:        'Homepage',
    l10n:         'Switch language',
    l10n_loading: 'Loading languages...'
  },
  pages: {
    not_found: {
      title: 'Error 404',
      text:  'It seems as this page doesn\'t exist.'
    },
    hello: {
      head: 'Hello World!'
    },
    portal: {
      head:           'Create Account',
      create_account: {
        info_box: 'Please fill all these fields. After that you\'ll get an activation email in order to activate your account.',
        form:     {
          username: 'Pick a username',
          password: 'Pick a password',
          email:    'Choose an email address',
          button:   'Create Account'
        },
        suggestions: 'The following suggestions for your name were generated:',
        success:     'The account has been created successfully. You can now activate your account using the activation email.'
      },
      activate: {
        progress: 'Activating account of ',
        success:  'Activation successful.',
        error:    'Activation failed. Is the activation expired?',
        headline: 'Activation page'
      },
      login: {
        headline:  'Landing Page',
        info_text: 'Lorem ipsum dolor sit amet, consetetur sadipscing elitr, sed diam nonumy eirmod tempor invidunt ut labore et dolore magna aliquyam erat, sed diam voluptua. At vero eos et accusam et justo duo dolores et ea rebum. Stet clita kasd gubergren, no sea takimata sanctus est Lorem ipsum dolor sit amet. Lorem ipsum dolor sit amet, consetetur sadipscing elitr, sed diam nonumy eirmod tempor invidunt ut labore et dolore magna aliquyam erat, sed diam voluptua. At vero eos et accusam et justo duo dolores et ea rebum. Stet clita kasd gubergren, no sea takimata sanctus est Lorem ipsum dolor sit amet.',
        form:      {
          username: 'Username',
          password: 'Password',
          button:   'Log in'
        },
        panels: {
          login: 'Log In',
          info:  'About Sententiaregum'
        }
      }
    },
    network: {
      logout:    'Logout',
      dashboard: {
        index: {
          title: 'Recent activity'
        }
      }
    }
  }
};
