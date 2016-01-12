/*
 * This file is part of the Sententiaregum project.
 *
 * (c) Maximilian Bosch <maximilian.bosch.27@gmail.com>
 * (c) Ben Bieler <benjaminbieler2014@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

import React from 'react';
import Translate from 'react-translate-component';

export default class HelloWorld extends React.Component {
  render() {
    return <Translate content="pages.hello.head" component="h1" />;
  }
}
