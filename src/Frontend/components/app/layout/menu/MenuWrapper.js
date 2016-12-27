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

/**
 * @author Maximilian Bosch <maximilian.bosch.27@gmail.com>
 */
export default class MenuWrapper extends Component {

  static propTypes = {
    children: React.PropTypes.node
  };

  static contextTypes = {
    router: React.PropTypes.oneOfType([
      React.PropTypes.func,
      React.PropTypes.object
    ])
  };

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
}
