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

import React                  from 'react';
import MenuWrapper            from './menu/MenuWrapper';
import Menu                   from './menu/Menu';
import menu                   from '../../../config/menu';
import { connect }            from 'react-redux';
import *  as menuActions      from '../../../actions/menuActions';
import { bindActionCreators } from 'redux';

const AppMenu = ({ items, actions }) => {
  return (
    <MenuWrapper>
      <Menu rawItems={menu} items={items} actions={actions}/>
    </MenuWrapper>
  );
};

AppMenu.propTypes = {
  config: React.PropTypes.arrayOf(React.PropTypes.object)
};

const mapStateToProps = state => {
  return ({
    items: state.menu.items
  });
};

const mapDispatchToProps = dispatch => ({
  actions: bindActionCreators(menuActions, dispatch)
});

export default connect(
  mapStateToProps,
  mapDispatchToProps
)(AppMenu);
