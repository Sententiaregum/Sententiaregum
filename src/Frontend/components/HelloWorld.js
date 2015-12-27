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
import Component from './app/Component';
import { portal } from '../config/menu';

export default class HelloWorld extends Component {
  getMenuData() {
    return portal;
  }
  renderPage() {
    return <h1>Hello World!</h1>;
  }
}
