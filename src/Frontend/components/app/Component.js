/*
 * This file is part of the Sententiaregum project.
 *
 * (c) Maximilian Bosch <maximilian.bosch.27@gmail.com>
 * (c) Ben Bieler <benjaminbieler2014@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

'use strict';

import React from 'react';
import Menu from './Menu';
import { ApiKey } from '../../util/http/facade/HttpServices';
import Url from '../../util/http/facade/Url';

/**
 * Enhanced base class for react components requiring a menu
 * and a configurable access control.
 *
 * @author Maximilian Bosch <maximilian.bosch.27@gmail.com>
 */
export default class Component extends React.Component {
  /**
   * Constructor.
   *
   * @param {Object.<Array>} props List of properties this component contains.
   *
   * @returns {void}
   */
  constructor(props) {
    super(props);

    this.authConfig = {};
  }

  /**
   * Returns a list of items to be shown in the menu with the following format:
   *
   * [
   *  { label: 'Label', url: '/#/url', logged_in: false, is_admin: false },
   *  // ...
   * ]
   *
   * @returns {Array} The given menu items.
   */
  getMenuData() {
    return [];
  }

  /**
   * Abstract method which should build the basic JSX tree.
   * This method must be used since the actual render() method adapts the result
   * of this method with the menu and a security check.
   *
   * @returns {React.DOM} The virtual dom of the actual page to be rendered.
   */
  renderPage() {
  }

  /**
   * Adapts the actual part of the component with the menu.
   *
   * @returns {React.DOM} The full page dom.
   */
  render() {
    let renderPage = true;
    if (!!this.authConfig.isLoggedIn && !ApiKey.isLoggedIn() || !!this.authConfig.isAdmin && !ApiKey.isAdmin()) {
      renderPage = false;
    }

    if (renderPage) {
      const menuItems = this.getMenuData();
      const basicPage = this.renderPage();

      return (
        <div className="container">
          <Menu items={menuItems} />
          {basicPage}
        </div>
      );
    }

    Url.redirect('');
    return <span>Redirecting...</span>;
  }
}
