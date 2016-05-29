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
import MenuWrapper from './menu/MenuWrapper';
import Menu from './menu/Menu';

/**
 * Component which simplifies the usage of the menu API.
 *
 * @param {Object} props The config for this wrapper component.
 *
 * @returns {React.Element} The markup of the menu.
 */
const AppMenu = props => {
  return (
    <MenuWrapper>
      <Menu items={props.config} />
    </MenuWrapper>
  );
};

AppMenu.propTypes = {
  config: React.PropTypes.arrayOf(React.PropTypes.object)
};

export default AppMenu;
