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
import React from 'react';
import LanguageSwitcher from '../../widgets/LanguageSwitcher';
import Nav from 'react-bootstrap/lib/Nav';

/**
 * Renderer for the menu markup.
 *
 * @param {Object} props The component properties.
 * @param {Object} context The component context.
 *
 * @returns {React.Element} The markup of the menu.
 */
const MenuWrapper = (props, context) => {
  return (
    <Navbar inverse fixedTop>
      <Navbar.Header>
        <Navbar.Brand>
          {context.router.isActive('/', true) ? <span>Sententiaregum</span> : <a href="/#/">Sententiaregum</a>}
        </Navbar.Brand>
        <Navbar.Toggle />
      </Navbar.Header>
      <Navbar.Collapse>
        <Nav>
          <LanguageSwitcher />
        </Nav>
        {props.children}
      </Navbar.Collapse>
    </Navbar>
  );
};

MenuWrapper.propTypes = {
  children: React.PropTypes.node
};

MenuWrapper.contextTypes = {
  router: React.PropTypes.oneOfType([
    React.PropTypes.func,
    React.PropTypes.object
  ])
};
export default MenuWrapper;
