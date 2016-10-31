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

import Navbar from 'react-bootstrap/lib/Navbar';
import React, { Component } from 'react';
import LanguageSwitcher from '../../widgets/LanguageSwitcher';
import Nav from 'react-bootstrap/lib/Nav';
import { TRANSFORM_ITEMS } from '../../../../constants/Menu';
import menuActions from '../../../../actions/menuActions';
import { connector, runAction } from 'sententiaregum-flux-container';
import userStore from '../../../../store/userStore';

export default class MenuWrapper extends Component {
  /**
   * Constructor.
   *
   * @param {Object} props The component properties.
   *
   * @returns {void}
   */
  constructor(props) {
    super(props);
    this.buildMenu = this.buildMenu.bind(this);
  }

  /**
   * Lifecycle hook which connects the menu with the app environment (security system and menu actions).
   *
   * @returns {void}
   */
  componentDidMount() {
    // when the component is mounted, the menu should be built the first time.
    this.buildMenu();

    // whenever data in the user store changes (e.g. login/logout), the menu needs a rebuild.
    connector(userStore).subscribe(this.buildMenu);
  }

  /**
   * Renderer for the menu markup.
   *
   * @returns {React.Element} The markup of the menu.
   */
  render() {
    return (
      <Navbar inverse fixedTop>
        <Navbar.Header>
          <Navbar.Brand>
            {this.context.router.isActive('/', true) ? <span>Sententiaregum</span> : <a href="/#/">Sententiaregum</a>}
          </Navbar.Brand>
          <Navbar.Toggle />
        </Navbar.Header>
        <Navbar.Collapse>
          <Nav>
            <LanguageSwitcher />
          </Nav>
          {this.props.children}
        </Navbar.Collapse>
      </Navbar>
    );
  }

  /**
   * Simple hook which recreates the menu after certain events (login/logout in the app for instance).
   *
   * @returns {void}
   */
  buildMenu() {
    runAction(TRANSFORM_ITEMS, menuActions, [this.props.items]);
  }
}

MenuWrapper.propTypes = {
  children: React.PropTypes.node,
  items:    React.PropTypes.array

};

MenuWrapper.contextTypes = {
  router: React.PropTypes.oneOfType([
    React.PropTypes.func,
    React.PropTypes.object
  ])
};
