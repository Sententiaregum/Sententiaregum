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

import ComponentBuilder from '../../../util/react/ComponentBuilder';
import React from 'react';
import chai from 'chai';

describe('ComponentBuilder', () => {
  it('builds a page rendering component', () => {
    const cmp = React.createClass({
      render: function () {
        return <h1>Test Component</h1>;
      }
    });

    const result = ComponentBuilder.buildGenericComponentForPage(cmp, [{ label: 'Test' }], {});
    const params = {
      ID: 1
    };

    const reactDecorator = result.apply(null, [{ params }]);
    chai.expect(reactDecorator.props.params).to.equal(params);
    chai.expect(reactDecorator.props.app).to.equal(cmp);
    chai.expect(reactDecorator.props.menuData[0].label).to.equal('Test');
  });
});
