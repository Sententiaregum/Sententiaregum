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
import MenuWrapper from './MenuWrapper';
import ApiKey from '../../util/http/ApiKeyService';

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
   * @returns {React.Element} The virtual dom of the actual page to be rendered.
   */
  renderPage() {
  }

  /**
   * Adapts the actual part of the component with the menu.
   *
   * @returns {React.Element|bool} The full page dom.
   */
  render() {
    let renderPage = true;
    if (!!this.authConfig.isLoggedIn && !ApiKey.isLoggedIn() || !!this.authConfig.isAdmin && !ApiKey.isAdmin()) {
      renderPage = false;
    }

    if (renderPage) {
      return (
        <div className="container">
          <MenuWrapper>
            <Menu items={this.getMenuData()} />
          </MenuWrapper>
          {this.renderPage()}
        </div>
      );
    }

    this.context.router.replace('/');

    // when returning false, an empty tag will be rendered, but no content.
    // until the redirect and re-render is complete (which won't take much time), nothing should be rendered.
    // Actually nothing can be seen, but an invariant violation must be avoided.
    return false;
  }
}

Component.contextTypes = {
  router: React.PropTypes.oneOfType([
    React.PropTypes.func,
    React.PropTypes.object
  ])
};
